<?php 
    
    function generateHiddenFields($excludeKey = '') {
        foreach ($_GET as $key => $value) {
            if ($key != $excludeKey) {
                echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
            }
        }
    }
    function createPaginationButton($currentPage, $totalPages, $buttonType){
        echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='get'>";
            generateHiddenFields('page_index');

            if ($buttonType == "prev"){
                $pageValue = $currentPage - 1;
                $buttonCondition = $currentPage > 1;
                $buttonText = "&laquo; Trang trước";
            } elseif ($buttonType == "next"){
                $pageValue = $currentPage + 1;
                $buttonCondition = $currentPage < $totalPages;
                $buttonText = "Trang tiếp &raquo;";
            }

            if ($buttonType == "dropdown"){
                echo "<select class='pagination-select' onchange='this.form.submit()' name='page_index'>";
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $selected = ($i == $currentPage) ? 'selected' : '';
                        echo "<option value='$i' $selected>Trang $i</option>";
                    }
                echo "</select>";
            } else {
                $buttonClass =  $buttonCondition ? "neutral-button" : "disabled-button";
                $buttonDisabled = !$buttonCondition ? "disabled" : "";
                $buttonTypeName = $buttonCondition ? "type='submit'" : "";
                echo "<button $buttonTypeName name='page_index' value='$pageValue' class='$buttonClass' $buttonDisabled>$buttonText</button>";
            }
        echo "</form>";
    }
    function createPagination($currentPage, $totalPages){
        createPaginationButton($currentPage, $totalPages, "prev");
        createPaginationButton($currentPage, $totalPages, "dropdown");
        createPaginationButton($currentPage, $totalPages, "next");
    }
?>