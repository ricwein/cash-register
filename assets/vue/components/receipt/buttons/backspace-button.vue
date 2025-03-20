<template>
  <div class="backspace-button d-flex align-items-center justify-content-around" @click="$emit('backspaceClicked')">
    <span class="fa-solid fa-2xl fa-backspace"></span>
  </div>
</template>

<script setup lang="ts">

</script>

<style scoped lang="scss">
.backspace-button {
  background-color: var(--bs-dark-bg-subtle);
  width: 20vw;
  min-width: 4rem;
  border-right: 0.05em solid var(--bs-dark);
  cursor: pointer;

  &:active, &:focus, &:hover {
    background-color: var(--bs-warning);

    * {
      color: var(--bs-danger);
    }
  }
}
</style>
