# PHPExcel Developer Documentation

## Reading and writing to file

As you already know from part  REF _Ref191885438 \w \h 3.3  REF _Ref191885438 \h Readers and writers, reading and writing to a persisted storage is not possible using the base PHPExcel classes. For this purpose, PHPExcel provides readers and writers, which are implementations of PHPExcel_Writer_IReader and PHPExcel_Writer_IWriter.

### PHPExcel_IOFactory

The PHPExcel API offers multiple methods to create a PHPExcel_Writer_IReader or PHPExcel_Writer_IWriter instance:

Direct creation via PHPExcel_IOFactory.  All examples underneath demonstrate the direct creation method. Note that you can also use the PHPExcel_IOFactory class to do this.

#### Creating PHPExcel_Reader_IReader using PHPExcel_IOFactory

There are 2 methods for reading in a file into PHPExcel: using automatic file type resolving or explicitly.

Automatic file type resolving checks the different PHPExcel_Reader_IReader distributed with PHPExcel. If one of them can load the specified file name, the file is loaded using that PHPExcel_Reader_IReader. Explicit mode requires you to specify which PHPExcel_Reader_IReader should be used.

You can create a PHPExcel_Reader_IReader instance using PHPExcel_IOFactory in automatic file type resolving mode using the following code sample:

```php
$objPHPExcel = PHPExcel_IOFactory::load("05featuredemo.xlsx");
```

A typical use of this feature is when you need to read files uploaded by your users, and you don’t know whether they are uploading xls or xlsx files.

If you need to set some properties on the reader, (e.g. to only read data, see more about this later), then you may instead want to use this variant:

```php
$objReader = PHPExcel_IOFactory::createReaderForFile("05featuredemo.xlsx");
$objReader->setReadDataOnly(true);
$objReader->load("05featuredemo.xlsx");
```

You can create a PHPExcel_Reader_IReader instance using PHPExcel_IOFactory in explicit mode using the following code sample:

```php
$objReader = PHPExcel_IOFactory::createReader("Excel2007");
$objPHPExcel = $objReader->load("05featuredemo.xlsx");
```

Note that automatic type resolving mode is slightly slower than explicit mode.

#### Creating PHPExcel_Writer_IWriter using PHPExcel_IOFactory

You can create a PHPExcel_Writer_Iwriter instance using PHPExcel_IOFactory:

```php
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
$objWriter->save("05featuredemo.xlsx");
```

### Excel 2007 (SpreadsheetML) file format

Excel2007 file format is the main file format of PHPExcel. It allows outputting the in-memory spreadsheet to a .xlsx file.

#### PHPExcel_Reader_Excel2007

##### Reading a spreadsheet

You can read an .xlsx file using the following code:

```php
$objReader = new PHPExcel_Reader_Excel2007();
$objPHPExcel = $objReader->load("05featuredemo.xlsx");
```

##### Read data only

You can set the option setReadDataOnly on the reader, to instruct the reader to ignore styling, data validation, … and just read cell data:

```php
$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load("05featuredemo.xlsx");
```

##### Read specific sheets only

You can set the option setLoadSheetsOnly on the reader, to instruct the reader to only load the sheets with a given name:

```php
$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setLoadSheetsOnly( array("Sheet 1", "My special sheet") );
$objPHPExcel = $objReader->load("05featuredemo.xlsx");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements PHPExcel_Reader_IReadFilter. By default, all cells are read using the PHPExcel_Reader_DefaultReadFilter.

The following code will only read row 1 and rows 20 – 30 of any sheet in the Excel file:

```php
class MyReadFilter implements PHPExcel_Reader_IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
}

$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadFilter( new MyReadFilter() );
$objPHPExcel = $objReader->load("06largescale.xlsx");
```

#### PHPExcel_Writer_Excel2007

##### Writing a spreadsheet

You can write an .xlsx file using the following code:

```php
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save("05featuredemo.xlsx");
```

##### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet. This can be slow on large spreadsheets, and maybe even unwanted. You can however disable formula pre-calculation:

```php
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->setPreCalculateFormulas(false);
$objWriter->save("05featuredemo.xlsx");
```

##### Office 2003 compatibility pack

Because of a bug in the Office2003 compatibility pack, there can be some small issues when opening Excel2007 spreadsheets (mostly related to formula calculation). You can enable Office2003 compatibility with the following code:

```
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->setOffice2003Compatibility(true);
$objWriter->save("05featuredemo.xlsx");
```

__Office2003 compatibility should only be used when needed__
Office2003 compatibility option should only be used when needed. This option disables several Office2007 file format options, resulting in a lower-featured Office2007 spreadsheet when this option is used.

### Excel 5 (BIFF) file format

Excel5 file format is the old Excel file format, implemented in PHPExcel to provide a uniform manner to create both .xlsx and .xls files. It is basically a modified version of [PEAR Spreadsheet_Excel_Writer][21], although it has been extended and has fewer limitations and more features than the old PEAR library. This can read all BIFF versions that use OLE2: BIFF5 (introduced with office 95) through BIFF8, but cannot read earlier versions.

Excel5 file format will not be developed any further, it just provides an additional file format for PHPExcel.

__Excel5 (BIFF) limitations__
Please note that BIFF file format has some limits regarding to styling cells and handling large spreadsheets via PHP.

#### PHPExcel_Reader_Excel5

##### Reading a spreadsheet

You can read an .xls file using the following code:

```php
$objReader = new PHPExcel_Reader_Excel5();
$objPHPExcel = $objReader->load("05featuredemo.xls");
```

##### Read data only

You can set the option setReadDataOnly on the reader, to instruct the reader to ignore styling, data validation, … and just read cell data:

```php
$objReader = new PHPExcel_Reader_Excel5();
$objReader->setReadDataOnly(true);
$objPHPExcel = $objReader->load("05featuredemo.xls");
```

##### Read specific sheets only

You can set the option setLoadSheetsOnly on the reader, to instruct the reader to only load the sheets with a given name:

```php
$objReader = new PHPExcel_Reader_Excel5();
$objReader->setLoadSheetsOnly( array("Sheet 1", "My special sheet") );
$objPHPExcel = $objReader->load("05featuredemo.xls");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements PHPExcel_Reader_IReadFilter. By default, all cells are read using the PHPExcel_Reader_DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the Excel file:

```php
class MyReadFilter implements PHPExcel_Reader_IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }
}

$objReader = new PHPExcel_Reader_Excel5();
$objReader->setReadFilter( new MyReadFilter() );
$objPHPExcel = $objReader->load("06largescale.xls");
```

#### PHPExcel_Writer_Excel5

##### Writing a spreadsheet

You can write an .xls file using the following code:

```php
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
$objWriter->save("05featuredemo.xls");
```

### Excel 2003 XML file format

Excel 2003 XML file format is a file format which can be used in older versions of Microsoft Excel.

__Excel 2003 XML limitations__
Please note that Excel 2003 XML format has some limits regarding to styling cells and handling large spreadsheets via PHP.

#### PHPExcel_Reader_Excel2003XML

##### Reading a spreadsheet

You can read an Excel 2003 .xml file using the following code:

```php
$objReader = new PHPExcel_Reader_Excel2003XML();
$objPHPExcel = $objReader->load("05featuredemo.xml");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements PHPExcel_Reader_IReadFilter. By default, all cells are read using the PHPExcel_Reader_DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the Excel file:

```php
class MyReadFilter implements PHPExcel_Reader_IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$objReader = new PHPExcel_Reader_Excel2003XML();
$objReader->setReadFilter( new MyReadFilter() );
$objPHPExcel = $objReader->load("06largescale.xml");
```

### Symbolic LinK (SYLK)

Symbolic Link (SYLK) is a Microsoft file format typically used to exchange data between applications, specifically spreadsheets. SYLK files conventionally have a .slk suffix. Composed of only displayable ANSI characters, it can be easily created and processed by other applications, such as databases.

__SYLK limitations__
Please note that SYLK file format has some limits regarding to styling cells and handling large spreadsheets via PHP.

#### PHPExcel_Reader_SYLK

##### Reading a spreadsheet

You can read an .slk file using the following code:

```php
$objReader = new PHPExcel_Reader_SYLK();
$objPHPExcel = $objReader->load("05featuredemo.slk");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements PHPExcel_Reader_IReadFilter. By default, all cells are read using the PHPExcel_Reader_DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the SYLK file:

```php
class MyReadFilter implements PHPExcel_Reader_IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$objReader = new PHPExcel_Reader_SYLK();
$objReader->setReadFilter( new MyReadFilter() );
$objPHPExcel = $objReader->load("06largescale.slk");
```

### Open/Libre Office (.ods)

Open Office or Libre Office .ods files are the standard file format for Open Office or Libre Office Calc files.

#### PHPExcel_Reader_OOCalc

##### Reading a spreadsheet

You can read an .ods file using the following code:

```php
$objReader = new PHPExcel_Reader_OOCalc();
$objPHPExcel = $objReader->load("05featuredemo.ods");
```

##### Read specific cells only

You can set the option setReadFilter on the reader, to instruct the reader to only load the cells which match a given rule. A read filter can be any class which implements PHPExcel_Reader_IReadFilter. By default, all cells are read using the PHPExcel_Reader_DefaultReadFilter.

The following code will only read row 1 and rows 20 to 30 of any sheet in the Calc file:

```php
class MyReadFilter implements PHPExcel_Reader_IReadFilter {

    public function readCell($column, $row, $worksheetName = '') {
        // Read title row and rows 20 - 30
        if ($row == 1 || ($row >= 20 && $row <= 30)) {
            return true;
        }
        return false;
    }

}

$objReader = new PHPExcel_Reader_OOcalc();
$objReader->setReadFilter( new MyReadFilter() );
$objPHPExcel = $objReader->load("06largescale.ods");
```

### CSV (Comma Separated Values)

CSV (Comma Separated Values) are often used as an import/export file format with other systems. PHPExcel allows reading and writing to CSV files.

__CSV limitations__
Please note that CSV file format has some limits regarding to styling cells, number formatting, ...

#### PHPExcel_Reader_CSV

##### Reading a CSV file

You can read a .csv file using the following code:

```php
$objReader = new PHPExcel_Reader_CSV();
$objPHPExcel = $objReader->load("sample.csv");
```

##### Setting CSV options

Often, CSV files are not really “comma separated”, or use semicolon (;) as a separator. You can instruct PHPExcel_Reader_CSV some options before reading a CSV file.

Note that PHPExcel_Reader_CSV by default assumes that the loaded CSV file is UTF-8 encoded. If you are reading CSV files that were created in Microsoft Office Excel the correct input encoding may rather be Windows-1252 (CP1252). Always make sure that the input encoding is set appropriately.

```php
$objReader = new PHPExcel_Reader_CSV();
$objReader->setInputEncoding('CP1252');
$objReader->setDelimiter(';');
$objReader->setEnclosure('');
$objReader->setLineEnding("\r\n");
$objReader->setSheetIndex(0);

$objPHPExcel = $objReader->load("sample.csv");
```

##### Read a specific worksheet

CSV files can only contain one worksheet. Therefore, you can specify which sheet to read from CSV:

```php
$objReader->setSheetIndex(0);
```

##### Read into existing spreadsheet

When working with CSV files, it might occur that you want to import CSV data into an existing PHPExcel object. The following code loads a CSV file into an existing $objPHPExcel containing some sheets, and imports onto the 6th sheet:

```php
$objReader = new PHPExcel_Reader_CSV();
$objReader->setDelimiter(';');
$objReader->setEnclosure('');
$objReader->setLineEnding("\r\n");
$objReader->setSheetIndex(5); 

$objReader->loadIntoExisting("05featuredemo.csv", $objPHPExcel);
```

#### PHPExcel_Writer_CSV

##### Writing a CSV file

You can write a .csv file using the following code:

```php
$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
$objWriter->save("05featuredemo.csv");
```

##### Setting CSV options

Often, CSV files are not really “comma separated”, or use semicolon (;) as a separator. You can instruct PHPExcel_Writer_CSV some options before writing a CSV file:

```php
$objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
$objWriter->setDelimiter(';');
$objWriter->setEnclosure('');
$objWriter->setLineEnding("\r\n");
$objWriter->setSheetIndex(0);

$objWriter->save("05featuredemo.csv");
```

##### Write a specific worksheet

CSV files can only contain one worksheet. Therefore, you can specify which sheet to write to CSV:

```php
$objWriter->setSheetIndex(0);
```

##### Formula pre-calculation

By default, this writer pre-calculates all formulas in the spreadsheet. This can be slow on large spreadsheets, and maybe even unwanted. You can