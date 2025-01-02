document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const convertBtn = document.getElementById('convert');
    const resetBtn = document.getElementById('reset');
    const resultDiv = document.getElementById('result');

    convertBtn.addEventListener('click', convertAmount);
    resetBtn.addEventListener('click', resetForm);

    function convertAmount() {
        const amount = parseFloat(amountInput.value);
        if (isNaN(amount)) {
            resultDiv.textContent = '请输入有效的金额';
            return;
        }

        fetch('shuzi.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.result) {
                resultDiv.textContent = data.result;
            } else {
                resultDiv.textContent = '转换失败，请重试';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.textContent = '转换失败，请重试';
        });
    }

    function resetForm() {
        amountInput.value = '';
        resultDiv.textContent = '';
    }
});