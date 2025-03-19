<template>
  <div v-if="useLandscapeMode" class="row w-100">
    <div class="receipt col-lg-4 col-md-5 col-sm-5">
      <NumberDisplaySide :price @register-confirmed="showCheckout = true"></NumberDisplaySide>
      <ProductHistorySide :products="cart" @backspace-clicked="backspaceClicked"></ProductHistorySide>
    </div>
    <div class="products col">
      <ProductSelectionTabbed v-if="useCategoryTabs" :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelectionTabbed>
      <ProductSelection v-else :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelection>
    </div>
  </div>
  <div v-else>
    <div class="sticky-top">
      <NumberDisplay :price :displayHeightPortrait @register-confirmed="showCheckout = true"></NumberDisplay>
      <ProductHistory :products="cart" :historyHeightPortrait @backspace-clicked="backspaceClicked"></ProductHistory>
    </div>
    <ProductSelectionTabbed v-if="useCategoryTabs" :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelectionTabbed>
    <ProductSelection v-else :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelection>
  </div>

  <Checkout v-model="showCheckout" :price :cart @checkoutCancelled="showCheckout = false" @createNewReceipt="resetCart"></Checkout>
</template>

<script setup lang="ts">
import {type PropType, ref} from "vue";
import NumberDisplay from "../components/number-display.vue";
import NumberDisplaySide from "../components/number-display-side.vue";
import ProductHistory from "../components/product-history.vue";
import ProductHistorySide from "../components/product-history-side.vue";
import ProductSelection from "../components/product-selection.vue";
import ProductSelectionTabbed from "../components/product-selection-tabbed.vue";
import Category from "../../model/category";
import Product from "../../model/product";
import Checkout from "../components/checkout.vue";

const props = defineProps({
  confirmEndpointUrl: String,
  startPrice: Number,
  categories: Object as PropType<Array<Category>>,
  gridWidthElements: Number,
  gridHeightElements: Number,
  useCategoryTabs: Boolean,
  useLandscapeMode: Boolean,
});

const showCheckout = ref(false)
const price = ref<number>(props.startPrice ?? 0.0)
const cart = ref<Product[]>([]);

// for portrait-mode
const displayHeightPortrait = props.useLandscapeMode ? '0' : '5rem'
const historyHeightPortrait = props.useLandscapeMode ? '0' : '7rem'

function productClicked(product: Product): void {
  cart.value.push(product)
  _recalculateCart()
}

function backspaceClicked(): void {
  cart.value.pop()
  _recalculateCart()
}

function _recalculateCart(): void {
  price.value = Number(
      cart.value
          .map((product: Product) => product.price)
          .reduce((price: number, prev: number): number => price + prev, 0.0)
  )
}

function resetCart(): void {
  cart.value = []
  _recalculateCart()
}
</script>

<style lang="scss">
body {
  user-select: none !important;
}
</style>

<style scoped lang="scss">
.row > * {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
</style>
