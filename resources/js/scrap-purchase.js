function initScrapPurchaseForm() {
    const scrapForm = document.querySelector('form[action*="purchase.add_scrap"]');
    if (!scrapForm) {
        return; // Exit if the specific scrap purchase form is not on the page
    }

    const weightInput = scrapForm.querySelector('#weight');
    const amountPerKgInput = scrapForm.querySelector('#amount_per_kg');
    const wastePercentageInput = scrapForm.querySelector('#waste_percentage');
    const wasteInKgInput = scrapForm.querySelector('#waste_in_kg');
    const netWeightInput = scrapForm.querySelector('#net_weight');
    const totalAmountInput = scrapForm.querySelector('#total_amount');
    const wasteAmountInput = scrapForm.querySelector('#waste_amount');
    const grandTotalInput = scrapForm.querySelector('#grand_total');
    const discountInput = scrapForm.querySelector('#discount');
    const paidAmountInput = scrapForm.querySelector('#paid_amount');
    const remainingAmountInput = scrapForm.querySelector('#remaining_amount');

    function calculate() {
        const weight = parseFloat(weightInput.value) || 0;
        const amountPerKg = parseFloat(amountPerKgInput.value) || 0;
        const wastePercentage = parseFloat(wastePercentageInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const paidAmount = parseFloat(paidAmountInput.value) || 0;

        const wasteInKg = weight * (wastePercentage / 100);
        const netWeight = weight - wasteInKg;
        const totalAmount = weight * amountPerKg;
        const wasteAmount = wasteInKg * amountPerKg;
        const grandTotal = totalAmount + wasteAmount; // Using original logic
        const remainingAmount = grandTotal - paidAmount - discount;

        wasteInKgInput.value = wasteInKg.toFixed(2);
        netWeightInput.value = netWeight.toFixed(2);
        totalAmountInput.value = totalAmount.toFixed(2);
        wasteAmountInput.value = wasteAmount.toFixed(2);
        grandTotalInput.value = grandTotal.toFixed(2);
        remainingAmountInput.value = remainingAmount.toFixed(2);
    }

    const inputs = [weightInput, amountPerKgInput, wastePercentageInput, discountInput, paidAmountInput];
    inputs.forEach(input => {
        if (input) {
            input.addEventListener('input', calculate);
        }
    });
}

// Run the setup function after the DOM is loaded.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrapPurchaseForm);
} else {
    initScrapPurchaseForm();
}
