<template>
  <h1 :class="size">
    <span class="badge border border-dark border-2 rounded-pill font-monospace" :class="{'bg-danger': price < 0.00, 'bg-light text-dark': price === 0.00, 'bg-primary': price > 0.00}">
      {{ NumberFormatter.format(price) }}
    </span>
  </h1>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../../components/number-formatter.ts";

defineProps({
  price: {type: Number, required: true},
  size: {type: String, default: 'h2'},
})
</script>
