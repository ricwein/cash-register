<template>
  <div v-if="useLandscapeMode" class="row w-100">
    <div class="receipt col-lg-4 col-md-5 col-sm-5">
      <number-display-side class="sticky-top" :price></number-display-side>
      <receipt-side :products="cart" @removeArticle="removeArticleByIndex"></receipt-side>
      <div class="row action-button-row sticky-bottom">
        <backspace-button class="col" @backspaceClicked="cart.pop()"></backspace-button>
        <confirm-button class="col" :price @registerConfirmed="showCheckout = true"></confirm-button>
      </div>
    </div>
    <div class="products col">
      <product-selection-tabbed v-if="showCategoryTabs" :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection-tabbed>
      <product-selection v-else :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection>
    </div>
  </div>
  <div v-else>
    <div class="sticky-top">
      <number-display :price :displayHeightPortrait @registerConfirmed="showCheckout = true"></number-display>
      <receipt :products="cart" :historyHeightPortrait @removeArticle="removeArticleByIndex" @backspaceClicked="cart.pop()"></receipt>
    </div>
    <product-selection-tabbed v-if="showCategoryTabs" :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection-tabbed>
    <product-selection v-else :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection>
  </div>

  <checkout v-model="showCheckout" :price :cart :confirmEndpointUrl @checkoutCancelled="showCheckout = false" @createNewReceipt="cart = []"></checkout>
</template>

<script setup lang="ts">
import {computed, type PropType, ref} from "vue";
import Category from "../../model/category";
import Product from "../../model/product";
import NumberDisplay from "../components/receipt/display/number-display.vue";
import NumberDisplaySide from "../components/receipt/display/number-display-side.vue";
import Receipt from "../components/receipt/receipt.vue";
import ReceiptSide from "../components/receipt/receipt-side.vue";
import ConfirmButton from "../components/receipt/buttons/confirm-button.vue";
import BackspaceButton from "../components/receipt/buttons/backspace-button.vue";
import ProductSelection from "../components/selection/product-selection.vue";
import ProductSelectionTabbed from "../components/selection/product-selection-tabbed.vue";
import Checkout from "../components/checkout.vue";

const props = defineProps({
  confirmEndpointUrl: {type: String, required: true},
  categories: {type: Object as PropType<Array<Category>>, required: true},
  useLandscapeMode: {type: Boolean, required: true},
  startPrice: {type: Number, default: 0.0},
  gridWidthElements: {type: Number, default: 5},
  useCategoryTabs: {type: Boolean, default: true},
});

const showCheckout = ref(false)
const cart = ref<Product[]>([]);
const price = computed<number>(() => cart.value
    .map((product: Product) => product.price)
    .reduce((price: number, prev: number): number => price + prev, 0.0)
)

const showCategoryTabs = computed(() => props.useCategoryTabs && (props.categories?.length ?? 0) > 1);

// for portrait-mode
const displayHeightPortrait = props.useLandscapeMode ? '0' : '5rem'
const historyHeightPortrait = props.useLandscapeMode ? '0' : '7rem'

function removeArticleByIndex(index: number): void {
  const normalizedIndex: number = (cart.value.length - 1) - index;
  cart.value.splice(normalizedIndex, 1)
}
</script>

<style lang="scss">
body {
  user-select: none !important;
}
</style>

<style scoped lang="scss">
.row {
  margin-left: 0 !important;
  margin-right: 0 !important;

  & > * {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
}

.receipt {
  border-right: 0.2rem solid var(--bs-dark);

  .action-button-row {
    height: 5rem;
  }
}
</style>
