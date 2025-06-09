<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <h2 class="text-xl font-semibold mb-2">Redirecting to secure payment...</h2>
      <p class="text-gray-600">Please wait while we redirect you to Stripe.</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stripeUrl: String
  },
  mounted() {
    // Redirect ngay khi component được mount
    window.location.href = this.stripeUrl;
  }
}
</script>