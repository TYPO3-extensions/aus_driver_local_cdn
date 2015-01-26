<?php
namespace AUS\AusDriverLocalCdn\Driver;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Resource\ResourceStorage;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Markus Hölzle <m.hoelzle@andersundsehr.com>, anders und sehr GmbH
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
 * Driver for local filesystem with public CDN domain
 *
 * @author Markus Hölzle <m.hoelzle@andersundsehr.com>
 */
class LocalCdnDriver extends \TYPO3\CMS\Core\Resource\Driver\LocalDriver {


	const DRIVER_TYPE = 'AusDriverLocalCdn';

	const EXTENSION_KEY = 'aus_driver_local_cdn';


	/**
	 * Returns the public URL to a file.
	 * For the local driver, this will always return a path relative to PATH_site.
	 *
	 * @param string $identifier
	 * @return string
	 * @throws \TYPO3\CMS\Core\Resource\Exception
	 */
	public function getPublicUrl($identifier) {
		$publicUrl = NULL;
		$this->configuration['publicUrl'] = trim($this->configuration['publicUrl'], '/') . '/';

		$uriParts = explode('/', ltrim($identifier, '/'));
		$uriParts = array_map('rawurlencode', $uriParts);
		$identifier = implode('/', $uriParts);
		$publicUrl = $this->configuration['publicUrl'] . $identifier;
		return $publicUrl;
	}
}
