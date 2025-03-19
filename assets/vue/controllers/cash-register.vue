<template>
  <div v-if="useLandscapeMode" class="row">
    <div class="sticky-top receipt col">
      <NumberDisplaySide :price @register-confirmed="registerConfirmed"></NumberDisplaySide>
      <ProductHistorySide :products="cart" @backspace-clicked="backspaceClicked"></ProductHistorySide>
    </div>
    <div class="products col-8">
      <ProductSelectionTabbed v-if="useCategoryTabs" :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelectionTabbed>
      <ProductSelection v-else :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelection>
    </div>
  </div>
  <div v-else>
    <div class="sticky-top">
      <NumberDisplay :price :displayHeightPortrait @register-confirmed="registerConfirmed"></NumberDisplay>
      <ProductHistory :products="cart" :historyHeightPortrait @backspace-clicked="backspaceClicked"></ProductHistory>
    </div>
    <ProductSelectionTabbed v-if="useCategoryTabs" :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelectionTabbed>
    <ProductSelection v-else :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements :gridHeightElements @product-clicked="productClicked"></ProductSelection>
  </div>
</template>

<script setup lang="ts">
import {type PropType, ref} from "vue";
import {toast} from "vue3-toastify";
import NumberDisplay from "../components/number-display.vue";
import NumberDisplaySide from "../components/number-display-side.vue";
import ProductHistory from "../components/product-history.vue";
import ProductHistorySide from "../components/product-history-side.vue";
import ProductSelection from "../components/product-selection.vue";
import ProductSelectionTabbed from "../components/product-selection-tabbed.vue";
import Category from "../../model/category";
import Product from "../../model/product";

const props = defineProps({
  confirmEndpointUrl: String,
  startPrice: Number,
  categories: Object as PropType<Array<Category>>,
  gridWidthElements: Number,
  gridHeightElements: Number,
  useCategoryTabs: Boolean,
  useLandscapeMode: Boolean,
});

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

function registerConfirmed(): void {
  let data: Record<number, number> = {};

  /** @var {Product} product */
  for (const product of cart.value) {
    if (!data.hasOwnProperty(product.id)) {
      data[product.id] = 0;
    }
    data[product.id]++;
  }

  fetch(props.confirmEndpointUrl ?? '', {
    method: 'PUT',
    body: JSON.stringify(data)
  })
      .then(response => response.json())
      .then(response => {
        if (response.hasOwnProperty('success') && response.success) {
          cart.value = [];
          _recalculateCart()

          toast.success('verarbeitet', {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
            autoClose: 1000,
          })
        } else if (response.hasOwnProperty('error')) {
          toast.error(response.error, {
            position: toast.POSITION.TOP_LEFT,
            theme: toast.THEME.DARK,
          })
        }
      })
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
