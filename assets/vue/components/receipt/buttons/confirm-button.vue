<template>
  <div
      class="confirm bg-primary d-flex align-items-center flex-column justify-content-around"
      :class="{disabled: price <= 0}"
      @click="price > 0.0 && $emit('registerConfirmed')"
  >
    <span class="fa-solid fa-cash-register fa-2xl mt-3"></span>
    <span>quittieren</span>
  </div>
</template>

<script setup lang="ts">
defineProps({
  price: {type: Number, required: true},
})
</script>

<style scoped lang="scss">
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
</style>
