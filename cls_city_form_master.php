<?php
class FormGenerator {
    private $formHtml;
    private $tableName;
    private $_mdl;

    public function __construct($tableName, $model = null) {
        global $_dbh;
        $this->tableName = $tableName;
        $this->_mdl = $model;
        $this->formHtml = "<div class='box-body'><div class='form-group row gy-2 mb-3 d-flex flex-column'>";

        $select = $_dbh->prepare("SELECT generator_options FROM tbl_generator_master WHERE table_name = ?");
        $select->bindParam(1, $this->tableName);

        if ($select->execute()) {
            $row = $select->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row["generator_options"])) {
                $generator_options = json_decode($row["generator_options"]);
                if (!empty($generator_options->field_name)) {
                    foreach ($generator_options->field_name as $i => $field_name) {
                        if ($field_name == 'country_id') {
                            continue; 
                        }

                        $required = in_array($field_name, $generator_options->field_required ?? []);
                        $disabled = in_array($field_name, $generator_options->is_disabled ?? []);
                        $value = isset($this->_mdl) ? ($this->_mdl->{"_" . $field_name} ?? '') : '';

                        $label = $generator_options->field_label[$i] ?? ucfirst(str_replace("_", " ", $field_name));
                        $fieldType = $generator_options->field_type[$i] ?? 'text';

                        $this->formHtml .= $this->generateFieldHtml($fieldType, $field_name, $label, $value, $required, $disabled);

                        if ($field_name == 'state_id') {
                            $countryValue = isset($this->_mdl) ? ($this->_mdl->_country_name ?? '') : '';
                            $this->formHtml .= $this->generateFieldHtml('text', 'country_name', 'Country', $countryValue, false, true);
                        }
                    }
                }
            }
        }

        $this->formHtml .= "</div></div>";
    }

    private function generateFieldHtml($type, $name, $label, $value, $required, $disabled) {
        $value = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
        $req = $required ? 'required' : '';
        $dis = $disabled ? 'disabled' : '';

        $labelHtml = "<label for='$name' class='col-4 col-sm-2 col-md-1 col-lg-1 control-label'>$label";
        if ($required) {
            $labelHtml .= "<span class='text-danger'>*</span>";
        }
        $labelHtml .= "</label>";

        $inputHtml = "";

        if ($type == 'select') {
            $options = $this->getDropdownMenu("tbl_" . explode("_", $name)[0] . "_master", explode("_", $name)[0] . "_name", $name);
            $inputHtml .= "<select class='form-select form-control' id='$name' name='$name' $req $dis>";
            foreach ($options as $option) {
                $optValue = $option['value'];
                $optLabel = $option['label'];
                $selected = ($optValue == $value) ? 'selected' : '';
                $inputHtml .= "<option value='$optValue' $selected>$optLabel</option>";
            }
            $inputHtml .= "</select>";
        } elseif ($type == 'textarea') {
            $inputHtml .= "<textarea id='$name' name='$name' class='form-control' $req $dis placeholder='$label'>$value</textarea>";
        } else {
            $inputHtml .= "<input type='$type' class='form-control' id='$name' name='$name' value='$value' $req $dis placeholder='$label'>";
        }

        return "$labelHtml<div class='col-8 col-sm-4 col-md-3 col-lg-2'>$inputHtml<div class='invalid-feedback'></div></div>";
    }

    private function getDropdownMenu($table, $label_field, $value_field) {
        global $_dbh;
        $stmt = $_dbh->prepare("SELECT $label_field, $value_field FROM $table ORDER BY $label_field ASC");
        if ($stmt->execute()) {
            return array_map(function ($row) use ($label_field, $value_field) {
                return [
                    'label' => htmlspecialchars($row[$label_field], ENT_QUOTES, 'UTF-8'),
                    'value' => htmlspecialchars($row[$value_field], ENT_QUOTES, 'UTF-8')
                ];
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        return [];
    }

    public function getForm() {
        return $this->formHtml;
    }

    public function fetchCountry() {
        global $_dbh;

        if (!isset($_POST['state_id'])) {
            echo json_encode([]);
            return;
        }

        $state_id = intval($_POST['state_id']);
        $columns = 'c.country_id AS country_id, c.country_name AS country_name';
        $tableName = 'tbl_state_master s JOIN tbl_country_master c ON s.country_id = c.country_id';
        $whereCondition = "s.state_id = :state_id";

        $stmt = $_dbh->prepare("CALL csms1_search_detail(:columns, :tableName, :whereCondition)");
        $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
        $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);
        $stmt->bindParam(':whereCondition', $whereCondition, PDO::PARAM_STR);
        $stmt->bindParam(':state_id', $state_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($row ?: []);
        } else {
            echo json_encode([]);
        }
    }
}
?>
<script>
$(document).ready(function() {
    function fetchCountry(stateId) {
        $.ajax({
            url: "", // should point to your PHP handler if required (like 'yourfile.php')
            type: "POST",
            data: { action: "fetchCountry", state_id: stateId },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data && data.country_id) {
                        $('#country_id').val(data.country_id);
                        $('#country_name').val(data.country_name);
                    } else {
                        console.error("No country data received.");
                    }
                } catch (error) {
                    console.error("Invalid JSON format:", response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    }

    $('#state_id').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            fetchCountry(stateId);
        }
    });
});
</script>
