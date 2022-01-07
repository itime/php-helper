<?php

namespace Xin\Excel\Factories;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use Xin\Excel\Concerns\MapsCsvSettings;
use Xin\Excel\Concerns\WithCustomCsvSettings;
use Xin\Excel\Concerns\WithLimit;
use Xin\Excel\Concerns\WithReadFilter;
use Xin\Excel\Concerns\WithStartRow;
use Xin\Excel\Filters\LimitFilter;

class ReaderFactory {

	use MapsCsvSettings;

	/**
	 * @param object $import
	 * @param string $readerType
	 *
	 * @return IReader
	 * @throws Exception
	 */
	public static function make($import, string $readerType): IReader {
		$reader = IOFactory::createReader($readerType);

		if (method_exists($reader, 'setReadDataOnly')) {
			$reader->setReadDataOnly(config('excel.imports.read_only', true));
		}

		if (method_exists($reader, 'setReadEmptyCells')) {
			$reader->setReadEmptyCells(!config('excel.imports.ignore_empty', false));
		}

		if ($reader instanceof Csv) {
			static::applyCsvSettings(config('excel.imports.csv', []));

			if ($import instanceof WithCustomCsvSettings) {
				static::applyCsvSettings($import->getCsvSettings());
			}

			$reader->setDelimiter(static::$delimiter);
			$reader->setEnclosure(static::$enclosure);
			$reader->setEscapeCharacter(static::$escapeCharacter);
			$reader->setContiguous(static::$contiguous);
			$reader->setInputEncoding(static::$inputEncoding);
		}

		if ($import instanceof WithReadFilter) {
			$reader->setReadFilter($import->readFilter());
		} elseif ($import instanceof WithLimit) {
			$reader->setReadFilter(new LimitFilter(
				$import instanceof WithStartRow ? $import->startRow() : 1,
				$import->limit()
			));
		}

		return $reader;
	}

}
