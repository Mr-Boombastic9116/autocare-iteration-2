document.addEventListener("DOMContentLoaded", function () {
    const companySelect = document.getElementById("company");
    const modelSelect   = document.getElementById("model");
    const yearSelect    = document.getElementById("year");
    const fuelSelect    = document.getElementById("fuel");
    const variantSelect = document.getElementById("variant");

    const modelGroup   = document.getElementById("model-group");
    const yearGroup    = document.getElementById("year-group");
    const fuelGroup    = document.getElementById("fuel-group");
    const variantGroup = document.getElementById("variant-group");
    const extraFields  = document.getElementById("extra-fields");

    if (companySelect) {
        // 1. COMPANY → MODEL
        companySelect.addEventListener("change", function () {
            let selectedOpt = this.options[this.selectedIndex];
            let companyId = selectedOpt ? (selectedOpt.dataset.id || "1") : "";

            if (!this.value) {
                if (modelGroup) modelGroup.style.display = "none";
                if (yearGroup) yearGroup.style.display = "none";
                if (fuelGroup) fuelGroup.style.display = "none";
                if (variantGroup) variantGroup.style.display = "none";
                if (extraFields) extraFields.style.display = "none";
                return;
            }

            fetch("ajax/get_models.php?company_id=" + companyId)
                .then(res => res.text())
                .then(data => {
                    if (modelSelect) modelSelect.innerHTML = data;
                    if (modelGroup) modelGroup.style.display = "block";
                })
                .catch(err => console.error("Error fetching models:", err));
        });
    }

    if (modelSelect) {
        // 2. MODEL → YEAR
        modelSelect.addEventListener("change", function () {
            if (!this.value) {
                if (yearGroup) yearGroup.style.display = "none";
                if (fuelGroup) fuelGroup.style.display = "none";
                if (variantGroup) variantGroup.style.display = "none";
                if (extraFields) extraFields.style.display = "none";
                return;
            }

            fetch("ajax/get_years.php")
                .then(res => res.text())
                .then(data => {
                    if (yearSelect) yearSelect.innerHTML = data;
                    if (yearGroup) yearGroup.style.display = "block";
                })
                .catch(err => console.error("Error fetching years:", err));
        });
    }

    if (yearSelect) {
        // 3. YEAR → FUEL
        yearSelect.addEventListener("change", function () {
            if (!this.value) {
                if (fuelGroup) fuelGroup.style.display = "none";
                if (variantGroup) variantGroup.style.display = "none";
                if (extraFields) extraFields.style.display = "none";
                return;
            }

            fetch("ajax/get_fuels.php")
                .then(res => res.text())
                .then(data => {
                    if (fuelSelect) fuelSelect.innerHTML = data;
                    if (fuelGroup) fuelGroup.style.display = "block";
                })
                .catch(err => console.error("Error fetching fuels:", err));
        });
    }

    if (fuelSelect) {
        // 4. FUEL → VARIANT
        fuelSelect.addEventListener("change", function () {
            if (!this.value) {
                if (variantGroup) variantGroup.style.display = "none";
                if (extraFields) extraFields.style.display = "none";
                return;
            }

            let modelOpt = modelSelect ? modelSelect.options[modelSelect.selectedIndex] : null;
            let yearOpt  = yearSelect ? yearSelect.options[yearSelect.selectedIndex] : null;
            let fuelOpt  = this.options[this.selectedIndex];

            let modelId = modelOpt ? (modelOpt.dataset.id || "1") : "1";
            let yearId  = yearOpt ? (yearOpt.dataset.id || "12") : "12";
            let fuelId  = fuelOpt ? (fuelOpt.dataset.id || "1") : "1";

            fetch(`ajax/get_variants.php?model_id=${modelId}&year_id=${yearId}&fuel_id=${fuelId}`)
                .then(res => res.text())
                .then(data => {
                    if (variantSelect) variantSelect.innerHTML = data;
                    if (variantGroup) variantGroup.style.display = "block";
                })
                .catch(err => console.error("Error fetching variants:", err));
        });
    }

    if (variantSelect) {
        // 5. VARIANT → SHOW EXTRA FIELDS
        variantSelect.addEventListener("change", function () {
            if (!this.value) {
                if (extraFields) extraFields.style.display = "none";
                return;
            }
            if (extraFields) extraFields.style.display = "block";
        });
    }
});