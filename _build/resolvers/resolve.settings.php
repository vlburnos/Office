<?php
/**
 * Resolve system settings
 * @var array $options
 */
if ($object->xpdo) {
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			/** @var modX $modx */
			$modx =& $object->xpdo;

			/** @var modSystemSetting $setting */
			if ($setting = $modx->getObject('modSystemSetting', 'allow_multiple_emails')) {
				$setting->set('value', 0);
				$setting->save();
			}
			if ($setting = $modx->getObject('modSystemSetting', 'ha.register_users')) {
				$setting->set('value', false);
				$setting->save();
			}

			if (!$lexicon = $modx->getCount('modLexiconEntry', array('name' => 'ha_register_disabled'))) {
				$modx->lexicon->load('ru:office:auth');
				$lexicon = $modx->newObject('modLexiconEntry', array(
					'name' => 'ha_register_disabled',
					'value' => $modx->lexicon('office_auth_err_ha_disabled', array(), 'ru'),
					'topic' => 'default',
					'namespace' => 'hybridauth',
					'language' => 'ru',
				));
				$lexicon->save();
				$modx->lexicon->load('en:office:auth');
				$lexicon = $modx->newObject('modLexiconEntry', array(
					'name' => 'ha_register_disabled',
					'value' => $modx->lexicon('office_auth_err_ha_disabled', array(), 'en'),
					'topic' => 'default',
					'namespace' => 'hybridauth',
					'language' => 'en',
				));
				$lexicon->save();
			}

			if ($setting = $modx->getObject('modSystemSetting', array('key' => 'office_ms2_order_product_fields'))) {
				$value = $setting->get('value');
				if ($value == 'product_pagetitleproduct_articleweightpricecountcost') {
					$value = 'name,product_article,weight,price,count,cost';
				}
				elseif (strpos($value, 'product_pagetitle') !== false) {
					$value = str_replace('product_pagetitle', 'name', $value);
				}
				if ($value != $setting->get('value')) {
					$setting->set('value', $value);
					$setting->save();
				}
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			$lexicons = $modx->getIterator('modLexiconEntry', array('name' => 'ha_register_disabled'));
			/** @var modLexiconEntry $lexicon */
			foreach ($lexicons as $lexicon) {
				$lexicon->remove();
			}
	}
}
return true;