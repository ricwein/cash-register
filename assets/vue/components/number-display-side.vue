<template>
  <div class="bg-white display-container d-flex justify-content-end">
    <span class="display">{{ NumberFormatter.format(price ?? 0.0) }}</span>
    <div class="confirm bg-primary d-flex align-items-center flex-column justify-content-around" :class="{disabled: (price ?? 0.0) <= 0}" @click="(price ?? 0.0) > 0.0 && $emit('register-confirmed')">
      <span class="fa-solid fa-paper-plane fa-2xl mt-3"></span>
      <span>quittieren</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../components/number-formatter";

defineProps({
  price: Number,
})
</script>

<style scoped lang="scss">
.display-container {
  text-align: right;
  border-right: 0.2rem solid var(--bs-dark);

  .display {
    height: 5rem;
    font-size: calc(3rem);
    font-family: var(--bs-font-monospace), monospace;
    margin-left: 1em;
    margin-right: 1em;
  }

  .confirm {
    border-left: 0.05em solid var(--bs-dark);
    width: 10vw;

    &:not(.disabled) {
      cursor: pointer;

      &:active, &:focus, &:hover {
        background-color: var(--bs-danger) !important;
      }
    }

    &.disabled {
      cursor: not-allowed;
      background-color: var(--bs-secondary) !important;
    }
  }
}
</style>
