<template>
  <div class="bg-white display-container d-flex justify-content-between">
    <div v-if="transactionState.kind !== 'None'" class="transaction-info">
      <div v-if="transactionState.kind === 'Pending'" class="badge font-monospace bg-warning shadow badge rounded-pill mt-1 ms-1">
        <span class="fa-solid fa-spinner fa-sm fa-pulse"></span>
        {{ transactionState.count }}
      </div>
      <div v-if="transactionState.kind === 'Sending'" class="badge font-monospace bg-primary shadow badge rounded-pill mt-1 ms-1">
        <span class="fa-solid fa-sync fa-sm fa-spin"></span>
        {{ transactionState.count }}
      </div>
      <div v-if="transactionState.kind === 'Success'" class="badge bg-success shadow badge rounded-pill mt-1 ms-1">
        <span class="fa-solid fa-check"></span>
      </div>
    </div>
    <span class="display ms-auto">{{ NumberFormatter.format(price) }}</span>
  </div>
</template>

<script setup lang="ts">
import {NumberFormatter} from "../../../../components/number-formatter.ts";
import {type TransactionState} from "../../../../components/transaction-state.ts";

defineProps({
  price: {type: Number, required: true},
})

const transactionState = defineModel<TransactionState>({required: true})
</script>

<style scoped lang="scss">
.display-container {
  text-align: right;

  .transaction-info {
    height: 100%;
  }

  .display {
    height: 5rem;
    font-size: calc(3rem);
    font-family: var(--bs-font-monospace), monospace;
    margin-left: 1em;
    margin-right: 1em;
  }
}
</style>
