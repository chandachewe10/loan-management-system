<script src="https://pay.lenco.co/js/v1/inline.js"></script>
<script>
    function makePaymentsWithLenco() {
        LencoPay.getPaid({
            key: "{{ env('LENCO_PUBLIC_KEY') }}",
            reference: 'ref-' + Date.now(),
            email: "{{ auth()->user()->email }}",
            amount: {{ $amount }},
            currency: "ZMW",
            channels: ["card", "mobile-money"],
            customer: {
                firstName: "{{ auth()->user()->name }}",
                lastName: "",
                phone: "",
            },
            onSuccess: function (response) {
                let formData = new FormData();
                formData.append("_token", "{{ csrf_token() }}");
                formData.append("data", JSON.stringify(response));

                fetch("{{ route('completeSubscription', $amount) }}", {
                    method: "POST",
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        window.location.href = "/admin";
                    } else {
                        console.error("Unexpected response:", data);
                        alert("Something went wrong: " + (data.message || ''));
                        window.location.href = "/admin";
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    alert("Failed to complete the request. Please try again.");
                });
            },
            onClose: function () {
                alert('Payment was not complete, please try again.');
                window.location.href = "/admin";
            },
            onConfirmationPending: function () {
                alert('Your subscription will be completed when the payment is confirmed');
            },
        });
    }

    // Auto-trigger on page load
    window.addEventListener('DOMContentLoaded', function () {
        makePaymentsWithLenco();
    });
</script>
