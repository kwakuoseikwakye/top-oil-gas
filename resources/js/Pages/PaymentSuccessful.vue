<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Payment Successful</div>
 
                    <div class="card-body">
                        Your payment has been successful! Thank you for your purchase.
                    </div> 
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['flash'],

    mounted() {
        if (this.flash.success) {
            alert(this.flash.success);
        }
    }
}
</script>
