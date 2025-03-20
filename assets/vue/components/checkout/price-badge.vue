<template>
  <h2 class="text-center">
    <span class="badge border border-dark border-2 rounded-pill font-monospace" :class="{'bg-danger': price < 0.00, 'bg-light text-dark': price === 0.00, 'bg-primary': price > 0.00}">
      {{ NumberFormatter.format(price) }}
    </span>
  </h2>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../../components/number-formatter.ts";

defineProps({
  price: {type: Number, required: true}
})
</script>
