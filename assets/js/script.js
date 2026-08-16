/**
 * Shared "Add Vehicle" cascading dropdown logic
 * (Company -> Model -> Year -> Fuel -> Variant).
 *
 * This file is loaded on multiple pages, so every handler checks
 * the element exists before wiring up — attaching to a missing
 * element throws and silently breaks any script below it.
 */
document.addEventListener("DOMContentLoaded", function () {

    const companyEl = document.getElementById("company");
    const modelEl = document.getElementById("model");
    const yearEl = document.getElementById("year");
    const fuelEl = document.getElementById("fuel");
    const variantEl = document.getElementById("variant");

    if (!companyEl || !modelEl || !yearEl || !fuelEl || !variantEl) {
        // Not the add-vehicle page — nothing to wire up here.
        return;
    }

    const modelGroup = document.getElementById("model-group");
    const yearGroup = document.getElementById("year-group");
    const fuelGroup = document.getElementById("fuel-group");
    const variantGroup = document.getElementById("variant-group");
    const extraFields = document.getElementById("extra-fields");

    function resetDownstream(fromLevel) {
        if (fromLevel <= 1 && modelGroup) { modelGroup.style.display = "none"; modelEl.innerHTML = ""; }
        if (fromLevel <= 2 && yearGroup) { yearGroup.style.display = "none"; yearEl.innerHTML = ""; }
        if (fromLevel <= 3 && fuelGroup) { fuelGroup.style.display = "none"; fuelEl.innerHTML = ""; }
        if (fromLevel <= 4 && variantGroup) { variantGroup.style.display = "none"; variantEl.innerHTML = ""; }
        if (extraFields) extraFields.style.display = "none";
    }

    // COMPANY -> MODEL
    companyEl.addEventListener("change", function () {
        resetDownstream(1);

        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.dataset.id) return;

        fetch("ajax/get_models.php?company_id=" + encodeURIComponent(selected.dataset.id))
            .then(res => res.text())
            .then(data => {
                modelEl.innerHTML = data;
                modelGroup.style.display = "block";
            })
            .catch(() => { /* fail quietly, user can retry by reselecting */ });
    });

    // MODEL -> YEAR
    modelEl.addEventListener("change", function () {
        resetDownstream(2);

        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.dataset.id) return;

        fetch("ajax/get_years.php")
            .then(res => res.text())
            .then(data => {
                yearEl.innerHTML = data;
                yearGroup.style.display = "block";
            });
    });

    // YEAR -> FUEL
    yearEl.addEventListener("change", function () {
        resetDownstream(3);

        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.dataset.id) return;

        fetch("ajax/get_fuels.php")
            .then(res => res.text())
            .then(data => {
                fuelEl.innerHTML = data;
                fuelGroup.style.display = "block";
            });
    });

    // FUEL -> VARIANT
    fuelEl.addEventListener("change", function () {
        resetDownstream(4);

        const fuelSelected = this.options[this.selectedIndex];
        if (!fuelSelected || !fuelSelected.dataset.id) return;

        const modelSelected = modelEl.selectedOptions[0];
        const yearSelected = yearEl.selectedOptions[0];
        if (!modelSelected || !modelSelected.dataset.id || !yearSelected || !yearSelected.dataset.id) return;

        const model_id = modelSelected.dataset.id;
        const year_id = yearSelected.dataset.id;
        const fuel_id = fuelSelected.dataset.id;

        fetch(`ajax/get_variants.php?model_id=${encodeURIComponent(model_id)}&year_id=${encodeURIComponent(year_id)}&fuel_id=${encodeURIComponent(fuel_id)}`)
            .then(res => res.text())
            .then(data => {
                variantEl.innerHTML = data;
                variantGroup.style.display = "block";
            });
    });

    // VARIANT -> SHOW TEXT FIELDS
    variantEl.addEventListener("change", function () {
        if (!this.value) return;
        if (extraFields) extraFields.style.display = "block";
    });

});
