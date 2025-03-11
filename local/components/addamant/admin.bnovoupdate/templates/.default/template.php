<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;

/** @var  $arResult */
/** @var  $arParams */
/** @var  $templateFolder */

Loc::loadMessages(__FILE__);

Extension::load("ui.datepicker");
?>

<tr class="adm-detail-file-row">
    <td>
        <label for="date_from">Дата от:</label>
        <input type="text" id="date_from" name="date_from" class="bx-calendar">
    </td>
</tr>

<tr class="adm-detail-file-row">
    <td>
        <label for="date_to">Дата до:</label>
        <input type="text" id="date_to" name="date_to" class="bx-calendar">
    </td>
</tr>

<tr class="adm-detail-file-row">
    <td>
        <input type="text" id="external_id" name="external_id" value="<?=$arParams['EXTERNAL_ID']?>" readonly>
    </td>
</tr>

<tr></tr>

<tr class="adm-detail-file-row">
    <td>
        <input type="button" class="adm-btn-save" id="update_bnovo" value="Обновить">
    </td>
</tr>
<tr class="adm-detail-file-row">
    <td>
        <p id="result_update"></p>
    </td>
</tr>


<script>
    (function () {
        const inputFrom = document.getElementById('date_from');
        const inputTo = document.getElementById('date_to');
        const inputExternalId = document.getElementById('external_id');
        const updateButton = document.getElementById('update_bnovo');
        const resultUpdate = document.getElementById('result_update');

        let pickerFrom = null;
        const getPickerFrom = () => {
            if (pickerFrom === null) {
                pickerFrom = new BX.UI.DatePicker.DatePicker({
                    targetNode: inputFrom,
                    inputField: inputFrom,
                    enableTime: false,
                    useInputEvents: false,
                });
            }

            return pickerFrom;
        };

        let pickerTo = null;
        const getPickerTo = () => {
            if (pickerTo === null) {
                pickerTo = new BX.UI.DatePicker.DatePicker({
                    targetNode: inputTo,
                    inputField: inputTo,
                    enableTime: false,
                    useInputEvents: false,
                });
            }

            return pickerTo;
        };

        const updateBnovo = () => {
            let dateFromValue = inputFrom.value;
            let dateToValue = inputTo.value;
            let externalId = inputExternalId.value;

            BX.ajax.runComponentAction('<?=$this->getComponent()->getName()?>', 'updateBnovo', {
                mode: 'class',
                data: {
                    dateFromValue: dateFromValue,
                    dateToValue: dateToValue,
                    externalId: externalId
                }
            }).then(function(response) {
                // Обработка успешного ответа
                console.log(response.data);
                resultUpdate.innerHTML = response.data.message;
            }).catch(function(error) {
                // Обработка ошибки
                console.error(error);
            });
        };

        BX.Event.bind(inputFrom, "click", () => getPickerFrom().show());
        BX.Event.bind(inputTo, "click", () => getPickerTo().show());
        BX.Event.bind(updateButton, "click", () => updateBnovo());
    })();
</script>
