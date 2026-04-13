const { test, expect } = require('@playwright/test');

// Логин перед каждым тестом
async function login(page) {
    await page.goto('/');
    await page.click('button:has-text("Вход")');
    await page.waitForSelector('#login input[name="user"]', { state: 'visible' });
    await page.fill('#login input[name="user"]', 'test@test.com');
    await page.fill('#login input[name="password"]', 'test123');
    await page.click('#login button:has-text("Войти")');
    await page.waitForTimeout(2000);
}

// Открыть фильтр и дождаться загрузки AJAX-попапа
async function openFilter(page) {
    await page.goto('/golosovanie');
    await page.click('#button_votes_filter');
    // Фильтр загружается через AJAX — ждём появления формы
    await page.waitForSelector('form[method="get"]', { state: 'attached', timeout: 10000 });
    await page.waitForTimeout(500);
}

// Открыть форму создания голосования
async function openNewVote(page) {
    await page.goto('/golosovanie');
    await page.waitForSelector('#button_votes_new', { state: 'visible', timeout: 10000 });
    await page.click('#button_votes_new');
    // Ждём загрузки AJAX-попапа (поле вопроса — textarea)
    await page.waitForSelector('textarea[name="question"]', { state: 'attached', timeout: 10000 });
    await page.waitForTimeout(500);
}

test.describe('Голосования — исправленные баги', () => {

    test.beforeEach(async ({ page }) => {
        await login(page);
    });

    // Баг 1: В фильтре поля «Тема», «Ранг», «Группа в индексе» — выборка производится
    test('Баг 1: Фильтр — поле «Тема» содержит варианты (multiple select)', async ({ page }) => {
        await openFilter(page);
        // select скрыт плагином el_select, но опции должны быть в DOM
        const options = await page.locator('select[name="sf12[]"] option').all();
        expect(options.length).toBeGreaterThan(0);
        // Ранг тоже должен содержать варианты
        const rankOptions = await page.locator('select[name="sf13[]"] option').all();
        expect(rankOptions.length).toBeGreaterThan(0);
    });

    // Баг 2: Очистка поля индекса скрывает группы
    test('Баг 2: Очистка индекса скрывает группы', async ({ page }) => {
        await openNewVote(page);

        // Вводим индекс (5 цифр) через JS чтобы обойти маску
        await page.evaluate(() => {
            const input = document.querySelector('#post_index');
            input.value = '12345';
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('keyup', { bubbles: true }));
        });
        await page.waitForTimeout(800);

        // Очищаем индекс
        await page.evaluate(() => {
            const input = document.querySelector('#post_index');
            input.value = '';
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('keyup', { bubbles: true }));
        });
        await page.waitForTimeout(800);

        // Родительский .item блок групп должен быть скрыт
        const groupsItem = page.locator('#groups').locator('xpath=ancestor::div[contains(@class,"item")]');
        await expect(groupsItem).toBeHidden();
    });

    // Баг 3: Фильтр по субъекту включает голосования для всех субъектов
    test('Баг 3: Фильтр — поле «Субъект» содержит варианты', async ({ page }) => {
        await openFilter(page);
        // select скрыт, но опции должны быть в DOM
        const options = await page.locator('select[name="region"] option').all();
        expect(options.length).toBeGreaterThan(0);
    });

    // Баг 4: «Группа в индексе» в фильтре работает
    test('Баг 4: Группа в индексе в фильтре присутствует в DOM', async ({ page }) => {
        await openFilter(page);
        // Поле группы должно существовать в DOM
        await expect(page.locator('#groups')).toBeAttached();
        // Поле индекса тоже присутствует
        await expect(page.locator('#fpost_index')).toBeAttached();
    });

    // Баг 5: Кнопка «На утверждение» (треугольник) скрыта при статусе 4
    test('Баг 5: Кнопка votes_approve отсутствует в строках таблицы', async ({ page }) => {
        await page.goto('/golosovanie');
        // Кнопка-треугольник в строках таблицы (не групповая) должна отсутствовать
        const rowApprove = page.locator('tr .votes_approve');
        const rowCount = await rowApprove.count();
        expect(rowCount).toBe(0);
    });

    // Баг 6: Датапикер не начинается с 1900 года
    test('Баг 6: minDate датапикера не раньше 2020 года', async ({ page }) => {
        await openNewVote(page);
        // Flatpickr с altInput: true создаёт рядом с hidden input видимый altInput с классом el_input
        // Кликаем по нему через JS чтобы открыть календарь
        await page.evaluate(() => {
            const hiddenInput = document.querySelector('#init_start');
            if (hiddenInput && hiddenInput._flatpickr) {
                hiddenInput._flatpickr.open();
            }
        });
        await page.waitForSelector('.flatpickr-calendar.open', { timeout: 5000 });
        // Проверяем текущий год в навигаторе
        const yearEl = page.locator('.flatpickr-calendar.open .cur-year');
        const year = parseInt(await yearEl.inputValue());
        expect(year).toBeGreaterThanOrEqual(2020);
        // Пробуем перейти на несколько месяцев назад и убедиться что не уходим в 1900
        for (let i = 0; i < 5; i++) {
            await page.click('.flatpickr-calendar.open .flatpickr-prev-month');
        }
        const yearAfter = parseInt(await yearEl.inputValue());
        expect(yearAfter).toBeGreaterThanOrEqual(2020);
    });

    // Баг 7: В фильтре нет полей «Улица» и «Номер дома»
    test('Баг 7: Поля «Улица» и «Номер дома» отсутствуют в фильтре', async ({ page }) => {
        await openFilter(page);
        // Проверяем что полей улицы и дома нет
        await expect(page.locator('input[name="sf10"]')).toHaveCount(0);
        await expect(page.locator('input[name="sf11"]')).toHaveCount(0);
        // Лейблов тоже не должно быть
        const labels = await page.locator('label').allTextContents();
        expect(labels.some(l => l.includes('Улица'))).toBeFalsy();
        expect(labels.some(l => l.includes('Номер дома'))).toBeFalsy();
    });

});
