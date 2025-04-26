<?php
include_once(__DIR__ . "/../config/connection.php");

class FormGenerator {
    private $formHtml;
    private $tableName;
    private $_mdl;

    public function __construct($tableName, $model = null) {
        global $_dbh;
        $this->tableName = $tableName;
        $this->_mdl = $model;
        $this->formHtml = "<div class='container-fluid'>";


        $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
        $select->bindParam(1, $this->tableName);

        if ($select->execute()) {
            $row = $select->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $generator_options = json_decode($row["generator_options"]);
                if ($generator_options) {
                    foreach ($generator_options->field_name as $i => $field_name) {
                        if ($field_name == 'country_id') {
                                continue;
                            }
                        $required = in_array($field_name, $generator_options->field_required ?? []);
                        $disabled = in_array($field_name, $generator_options->is_disabled ?? []);
                        $value = isset($this->_mdl) ? ($this->_mdl->{"_" . $field_name} ?? '') : '';

                        switch ($generator_options->field_type[$i]) {
                            case 'text':
                            case 'email':
                            case 'password':
                                $this->addInput($generator_options->field_type[$i], $field_name, $generator_options->field_label[$i], $value, $required, $disabled);
                                break;
                            case 'textarea':
                                $this->addTextarea($field_name, $generator_options->field_label[$i], $value, $required, $disabled);
                                break;
                            case 'select':
                                $options = $this->getDropdownMenu("tbl_" . explode("_", $field_name)[0] . "_master", explode("_", $field_name)[0] . "_name", $field_name);
                                $this->addSelect($field_name, $generator_options->field_label[$i], $options, $value, $required, $disabled);
                                break;
                        }
                        if ($field_name == 'state_id') {
                            $countryValue = isset($this->_mdl) ? ($this->_mdl->_country_name ?? '') : '';
                            $countryId = isset($this->_mdl) ? ($this->_mdl->_country_id ?? '') : '';

                            if (!isset($_POST['country_name'])) {
                                $this->addInput('text', 'country_name', 'Country Name', $countryValue, false, true);
                            }

                        }
                    }
                }
            }
        }
    }
 public function addInput($type, $name, $label = '', $value = '', $required = false, $disabled = false) {
    $req = $required ? 'required' : '';
    $dis = $disabled ? 'disabled' : '';
    $value = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');

    $this->formHtml .= "<div class='row '>
                            <div class='col-md-12'>
                                <label for='$name' class='form-label fw-semibold'>$label</label>
                                <input type='$type' class='form-control' id='$name' name='$name' value='$value' $req $dis>
                            </div>
                        </div>";
}

public function addTextarea($name, $label = '', $value = '', $required = false, $disabled = false) {
    $req = $required ? 'required' : '';
    $dis = $disabled ? 'disabled' : '';
    $value = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');

    $this->formHtml .= "<div class='row'>
                            <div class='col-md-12'>
                                <label for='$name' class='form-label fw-semibold'>$label</label>
                                <textarea class='form-control' id='$name' name='$name' rows='3' $req $dis>$value</textarea>
                            </div>
                        </div>";
}

public function addSelect($name, $label = '', $options = [], $selected = '', $required = false, $disabled = false) {
    $req = $required ? 'required' : '';
    $dis = $disabled ? 'disabled' : '';
    $selected = htmlspecialchars($selected ?? '', ENT_QUOTES, 'UTF-8');
    $onchange = ($name == 'state_id') ? "onchange='fetchCountry(this.value)'" : '';


    $this->formHtml .= "<div class='row '>
                            <div class='col-md-12'>
                                <label for='$name' class='form-label fw-semibold'>$label</label>
                                <select class='form-select' id='$name' name='$name' $req $dis $onchange>";

    foreach ($options as $option) {
        $value = htmlspecialchars($option['value'] ?? '', ENT_QUOTES, 'UTF-8');
        $text = htmlspecialchars($option['label'] ?? '', ENT_QUOTES, 'UTF-8');
        $sel = ($value == $selected) ? 'selected' : '';
        $this->formHtml .= "<option value='$value' $sel>$text</option>";
    }

    $this->formHtml .= "</select>
                            </div>
                        </div>";
}
public function getForm() {

    return $this->formHtml;
}
    private function getDropdownMenu($table, $label_field, $value_field) {
        global $_dbh;
        $stmt = $_dbh->prepare("SELECT $label_field, $value_field FROM $table ORDER BY $label_field");
        if ($stmt->execute()) {
            return array_map(fn($row) => [
                'label' => htmlspecialchars($row[$label_field] ?? '', ENT_QUOTES, 'UTF-8'),
                'value' => htmlspecialchars($row[$value_field] ?? '', ENT_QUOTES, 'UTF-8')
            ], $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        return [];
    }
}
?>
<script>
    function fetchCountry(stateId) {
    $.ajax({
        url: "classes/cls_customer_master.php",
        type: "POST",
        data: { action: "fetchCountry", state_id: stateId },
        success: function (response) {
            try {
                const data = JSON.parse(response);
                if (data && data.country_id) {
                    $('#country_id').val(data.country_id);
                    $('#country_name').val(data.country_name);
                } else {
                    console.error("Country data is missing in the response.");
                }
            } catch (e) {
                console.error("Invalid JSON:", response);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    }); 
}
            const stateField = document.getElementById("state_id");
        if (stateField) {
            stateField.addEventListener("change", function() {
                fetchCountry(this.value);
            });
        }
</script>
