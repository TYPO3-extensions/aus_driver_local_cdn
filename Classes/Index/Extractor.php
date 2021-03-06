<?php
namespace AUS\AusDriverLocalCdn\Index;

use AUS\AusDriverLocalCdn\Driver\LocalCdnDriver;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Index\ExtractorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Markus Hölzle <m.hoelzle@andersundsehr.com>, anders und sehr GmbH
 *  Stefan Lamm <s.lamm@andersundsehr.com>, anders und sehr GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Extractor for local imagefiles with public CDN domain
 *
 * @author Markus Hölzle <m.hoelzle@andersundsehr.com>
 * @author Stefan Lamm <s.lamm@andersundsehr.com>
 */
class Extractor implements ExtractorInterface {

	/**
	 * Returns an array of supported file types;
	 * An empty array indicates all filetypes
	 *
	 * @return array
	 */
	public function getFileTypeRestrictions() {
		return array(File::FILETYPE_IMAGE);
	}


	/**
	 * Get all supported DriverClasses
	 *
	 * Since some extractors may only work for local files, and other extractors
	 * are especially made for grabbing data from remote.
	 *
	 * Returns array of string with driver names of Drivers which are supported,
	 * If the driver did not register a name, it's the classname.
	 * empty array indicates no restrictions
	 *
	 * @return array
	 */
	public function getDriverRestrictions() {
		return array(LocalCdnDriver::DRIVER_TYPE);
	}


	/**
	 * Returns the data priority of the extraction Service.
	 * Defines the precedence of Data if several extractors
	 * extracted the same property.
	 *
	 * Should be between 1 and 100, 100 is more important than 1
	 *
	 * @return integer
	 */
	public function getPriority() {
		return 50;
	}


	/**
	 * Returns the execution priority of the extraction Service
	 * Should be between 1 and 100, 100 means runs as first service, 1 runs at last service
	 *
	 * @return integer
	 */
	public function getExecutionPriority() {
		return 50;
	}


	/**
	 * Checks if the given file can be processed by this Extractor
	 *
	 * @param File $file
	 * @return boolean
	 */
	public function canProcess(File $file) {
		if ($file->getType() == File::FILETYPE_IMAGE &&
			$file->getStorage()->getDriverType() === LocalCdnDriver::DRIVER_TYPE) {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * The actual processing TASK
	 *
	 * Should return an array with database properties for sys_file_metadata to write
	 *
	 * @param File $file
	 * @param array         $previousExtractedData optional, contains the array of already extracted data
	 * @return array
	 */
	public function extractMetaData(File $file, array $previousExtractedData = array()) {
		if (!$previousExtractedData['width'] || !$previousExtractedData['height']) {
			$imageDimensions = self::getImageDimensionsOfRemoteFile($file);
			if ($imageDimensions !== NULL) {
				$previousExtractedData['width'] = $imageDimensions[0];
				$previousExtractedData['height'] = $imageDimensions[1];
			}
		}

		return $previousExtractedData;
	}


	/**
	 * @param FileInterface $file
	 * @return array|NULL
	 */
	public static function getImageDimensionsOfRemoteFile(FileInterface $file){
		/* @var $graphicalFunctionsObject \TYPO3\CMS\Core\Imaging\GraphicalFunctions */
		$graphicalFunctionsObject = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Imaging\\GraphicalFunctions');
		$tempFilePath = $file->getForLocalProcessing();
		return $graphicalFunctionsObject->getImageDimensions($tempFilePath);
	}

}