				<section>
					<div class="order">
						<div class="box">
							<div class="bread">
								<ul>
									<li><a href="/">Главная</a></li>
									<li class="last">Оформление заказа</li>
								</ul>
							</div>
						</div>
						<!-- form order -->
						<?
						if(!isset($_SESSION['login'])){
						?>
						<div class="box">
							<div class="item">
								<div class="contact">
									<div class="title">Войдите в свой аккаунт</div>

									<p><span class="notify"></span>Новые покупатели будут зарегистрированы автоматически после оформления заказа</p>
									<button class="button" id="enter_lk" type="button">Войти</button>
								</div>
							</div>
						</div>
							<?
						}
							?>
						<form action="" method="post" name="form_order">
							<div class="box">
								<div class="item">
									<div class="logistic">
										<div class="title">Доставка</div>
										<div class="sub_title">Населённый пункт</div>
										<input type="text" name="city" value="" placeholder="Город, посёлок, деревня" />
										<div class="sub_title">Способ доставки</div>
										<div class="services">
											<div class="service">
												<div class="block">
													<label class="radiocontainer">Курьером в пределах МКАД + 5км.
														<div class="price">от 200</div>
														<p>Срок доставки: 1-3 дня. Более 5км. от МКАД стоимость доставки +100р. за каждый километр.</p>
														<input type="radio" name="logist"> <span class="checkmark"></span>
													</label>
												</div>
											</div>
											<div class="service">
												<div class="block">
													<label class="radiocontainer">Пункт выдачи заказов
														<div class="price">от 150</div>
														<p>Срок доставки: 2-3 дня.</p>
														<input type="radio" name="logist"> <span class="checkmark"></span>
													</label>
												</div>
											</div>
											<div class="service">
												<div class="block">
													<label class="radiocontainer">Постамат
														<div class="price">от 100</div>
														<p>Срок доставки: 1-3 дня.</p>
														<input type="radio" name="logist"> <span class="checkmark"></span>
													</label>
												</div>
											</div>
											<div class="service">
												<div class="block">
													<label class="radiocontainer">Транспортная компания
														<div class="price">от 500</div>
														<p>Срок доставки: 5-8 дней.</p>
														<input type="radio" name="logist"> <span class="checkmark"></span>
													</label>
												</div>
											</div>
										</div>
										<div class="widget">Виджет логистической конторы




										</div>
									</div>
								</div>
							</div>
							<div class="box">
								<div class="item">
									<div class="payment">
										<div class="title">Оплата</div>
										<div class="services">
											<div class="service">
												<div class="block">
													<label class="radiocontainer">Картой на сайте
														<p>Ссылка на сраницу оплаты будет в письме после подтверждения заказа</p>
														<input type="radio" name="payment"> <span class="checkmark"></span> </label>
												</div>
											</div>
											<div class="service">
												<div class="block">
													<label class="radiocontainer">При получении
														<p>Картой или наличными в пунктах выдачи заказов или постаматах</p>
														<input type="radio" name="payment"> <span class="checkmark"></span> </label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="box">
								<div class="item">
									<div class="payment">
										<div class="title">Бонусные баллы</div>
										<div class="services">
											<div class="service">
												<div class="block">
													<label class="radiocontainer">Использовать бонусы
														<p>Оплатить часть заказа ранее накопленными бонусами</p>
														<input type="radio" name="bonus"> <span class="checkmark"></span> </label>
												</div>
											</div>
											<div class="service">
												<div class="block">
													<label class="radiocontainer">Не использовать
														<p>Бонусные баллы за покупку добавятся к ранее накопленным</p>
														<input type="radio" name="bonus"> <span class="checkmark"></span> </label>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="box">
								<div class="item">
									<div class="contact">
										<div class="title">Контакты</div>
										<div class="sub_title">Фамилия, имя, отчество</div>
										<input type="text" name="name" value="" placeholder="Ф.И.О" />
										<div class="sub_title">Телефон</div>
										<input type="text" name="phone" value="" placeholder="+7 000 000-00-00" />
										<div class="sub_title">Почта</div>
										<input type="text" name="mail" value="" placeholder="elecrto@pochta.ru" />
										<div class="sub_title">Комментарий к заказу</div>
										<textarea name="comment" placeholder="Ваши пожелания к заказу"></textarea>
									</div>
								</div>
							</div>
							<div class="box">
								<div class="politic">
									<input type="checkbox" name="confirm_reg_policy" value="Y"> Я ознакомлен и согласен с условиями <a target="_blank" href="#">политики конфиденциальности</a></div>
								<input class="button" name="submit_order" id="submit_order" type="submit" value="Заказать">
							</div>
						</form>
						<!-- end form order -->
					</div>
				</section>