<template>
  <div class="bg-dark-subtle overflow-y-scroll receipt rounded p-3 shadow">
    <table class="table table-borderless table-sm">
      <tbody>
      <tr v-for="item in groupedArticles">
        <td class="bg-dark-subtle">{{ item.quantity }}<span class="fa-solid fa-times"></span></td>
        <td class="bg-dark-subtle"><span :class="item.product.icon"></span> {{ item.product.name }}</td>
        <td class="bg-dark-subtle">{{ NumberFormatter.format(item.quantity * item.product.price) }}</td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="2" class="bg-dark-subtle text-right">∑</td>
        <th class="bg-dark-subtle fw-bold border-top border-dark" ref="priceSum">{{ NumberFormatter.format(price) }}</th>
      </tr>
      </tfoot>
    </table>
  </div>
</template>

<script setup lang="ts">
import {computed} from "vue";
import {templateRef} from "@vueuse/core";
import type CartItem from "../../../model/cart-item.ts";
import {NumberFormatter} from "../../../components/number-formatter.ts";

const props = defineProps({
  price: {type: Number, required: true},
  cart: {type: Array<CartItem>, required: true},
})
const priceSum = templateRef('priceSum')

const groupedArticles = computed(() => props.cart)

setTimeout(() => priceSum.value.scrollIntoView({behavior: 'smooth'}), 200)
</script>

<style scoped lang="scss">
.receipt {
  max-height: calc(100vh - 205px);
}
</style>
