<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('mw_dailydeal')} ADD `sold_qty` int(11) default 0;

    ");

$installer->endSetup(); 