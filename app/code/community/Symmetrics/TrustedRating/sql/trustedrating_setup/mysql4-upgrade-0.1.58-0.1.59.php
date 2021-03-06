<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * Add trusted rating polish email to standard template emails.
 *
 * @category  Symmetrics
 * @package   Symmetrics_TrustedRating
 * @author    symmetrics - a CGI Group brand <info@symmetrics.de>
 * @author    Toni Stache <ts@symmetrics.de>
 * @copyright 2012 symmetrics - a CGI Group brand
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.symmetrics.de/
 */

/* @var Symmetrics_TrustedRating_Model_Setup */
$installer = $this;
$installer->startSetup();

foreach ($this->getConfigEmails() as $name => $data) {
    $templateId = $this->createEmail($data);
    if ($name == 'trustedrating_mail_de') {
        $installer->setConfigData('trustedrating/trustedrating_email/template', $templateId);
    }
}

$installer->endSetup();
