<template>
  <div class="bg-white display-container d-flex justify-content-end">
    <span class="display">{{ NumberFormatter.format(price) }}</span>
  </div>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../../../components/number-formatter.ts";

defineProps({
  price: {type: Number, required: true},
})
</script>

<style scoped lang="scss">
.display-container {
  text-align: right;

  .display {
    height: 5rem;
    font-size: calc(3rem);
    font-family: var(--bs-font-monospace), monospace;
    margin-left: 1em;
    margin-right: 1em;
  }
}
</style>
