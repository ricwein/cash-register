<template>
  <div class="w-100 bg-white display-container d-flex justify-content-end">
    <span class="display">{{ NumberFormatter.format(price) }}</span>
    <confirm-button :price @registerConfirmed="$emit('registerConfirmed')"></confirm-button>
  </div>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../../../components/number-formatter.ts";
import ConfirmButton from "../buttons/confirm-button.vue";

defineProps({
  price: {type: Number, required: true},
  displayHeightPortrait: String,
})
</script>

<style scoped lang="scss">
.display-container {
  height: v-bind(displayHeightPortrait);
  text-align: right;

  .display {
    font-size: calc(v-bind(displayHeightPortrait) - 2rem);
    font-family: var(--bs-font-monospace), monospace;
    margin-left: 1em;
    margin-right: 1em;
  }
}
</style>
