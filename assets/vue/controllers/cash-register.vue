<template>
  <div v-if="useLandscapeMode" class="row w-100">
    <div class="receipt col-lg-4 col-md-5 col-sm-5">
      <number-display-side v-model="transactionState" :transactionState class="sticky-top" :price></number-display-side>
      <receipt-side :cart @removeArticle="removeArticleByIndex"></receipt-side>
      <div v-if="quickCheckout" class="row action-button-row sticky-bottom">
        <backspace-button class="col" :buttonSound @backspaceClicked="cart.pop()" @createNewReceipt="reset"></backspace-button>
        <checkout-button class="col" :buttonSound :cart @registerConfirmed="checkoutState.dispatch(CheckoutTransition.Card)" :type="CheckoutTransition.Card"></checkout-button>
        <checkout-button class="col" :buttonSound :cart @registerConfirmed="checkoutState.dispatch(CheckoutTransition.Cash)" :type="CheckoutTransition.Cash"></checkout-button>
      </div>
      <div v-else class="row action-button-row sticky-bottom">
        <backspace-button class="col" :buttonSound @backspaceClicked="cart.pop()" @createNewReceipt="reset"></backspace-button>
        <checkout-button class="col" :buttonSound :cart @registerConfirmed="checkoutState.dispatch(CheckoutTransition.Start)"></checkout-button>
      </div>
    </div>
    <div class="products col">
      <product-selection-tabbed v-if="showCategoryTabs" :buttonSound :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection-tabbed>
      <product-selection v-else :buttonSound :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection>
    </div>
  </div>
  <div v-else>
    <div class="sticky-top">
      <number-display v-model="transactionState" :quickCheckout :buttonSound :transactionState :price :cart :displayHeightPortrait @registerConfirmed="(transition:CheckoutTransition) => checkoutState.dispatch(transition)"></number-display>
      <receipt :buttonSound :cart :historyHeightPortrait @removeArticle="removeArticleByIndex" @backspaceClicked="cart.pop()" @createNewReceipt="reset"></receipt>
    </div>
    <product-selection-tabbed v-if="showCategoryTabs" :buttonSound :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection-tabbed>
    <product-selection v-else :buttonSound :categories :displayHeightPortrait :historyHeightPortrait :gridWidthElements @product-clicked="product => cart.push(product)"></product-selection>
  </div>

  <checkout
      v-model="checkoutState"
      :buttonSound :lazyCalculator :price :cart :transactor
      @checkoutCancelled="checkoutState.dispatch(CheckoutTransition.Cancel)"
      @createNewReceipt="reset"
  ></checkout>
</template>

<script setup lang="ts">
import {computed, onMounted, type PropType, ref, shallowRef} from "vue";
import {useSound} from "@vueuse/sound";
import {CheckoutStateMachine, CheckoutTransition} from "../../components/checkout-state-machine.ts";
import Category from "../../model/category";
import Product from "../../model/product";
import NumberDisplay from "../components/receipt/display/number-display.vue";
import NumberDisplaySide from "../components/receipt/display/number-display-side.vue";
import Receipt from "../components/receipt/receipt.vue";
import ReceiptSide from "../components/receipt/receipt-side.vue";
import CheckoutButton from "../components/receipt/buttons/checkout-button.vue";
import BackspaceButton from "../components/receipt/buttons/backspace-button.vue";
import ProductSelection from "../components/selection/product-selection.vue";
import ProductSelectionTabbed from "../components/selection/product-selection-tabbed.vue";
import Checkout from "../components/checkout/checkout.vue";
import Transactor from "../../components/transactor.ts";
import {None, Pending, Sending, Success, type TransactionState} from "../../components/transaction-state.ts";
import successSound from '../../sounds/success.wav'

const props = defineProps({
  confirmEndpointUrl: {type: String, required: true},
  categories: {type: Object as PropType<Array<Category>>, required: true},
  useLandscapeMode: {type: Boolean, default: true},
  buttonSound: {type: Boolean, default: true},
  quickCheckout: {type: Boolean, default: true},
  lazyCalculator: {type: Boolean, default: true},
  startPrice: {type: Number, default: 0.0},
  gridWidthElements: {type: Number, default: 5},
  useCategoryTabs: {type: Boolean, default: true},
  enqueuedMessages: {type: String, default: null},
});

const checkoutState = shallowRef<CheckoutStateMachine>(new CheckoutStateMachine())
const transactor = new Transactor(decodeURI(props.confirmEndpointUrl), props.enqueuedMessages)
const transactionState = shallowRef<TransactionState>(new None())

checkoutState.value.addCallback(CheckoutTransition.RetryableError, () => {
  if (transactionState.value.kind === 'None') {
    transactionState.value = new Pending(1)
  } else if (transactionState.value instanceof Pending) {
    transactionState.value = new Pending(transactionState.value.count + 1)
  }
})

const cart = ref<Product[]>([])
const price = computed<number>(() => cart.value
    .map((product: Product) => product.price)
    .reduce((price: number, prev: number): number => price + prev, 0.0)
)

const showCategoryTabs = computed(() => props.useCategoryTabs && (props.categories?.length ?? 0) > 1);

// for portrait-mode
const displayHeightPortrait = props.useLandscapeMode ? '0' : '5rem'
const historyHeightPortrait = props.useLandscapeMode ? '0' : '7rem'

const {play: playSuccess} = useSound(successSound)

onMounted(() => {
  setInterval(() => {
    sendOpenTransactions()
  }, 5_000)
  sendOpenTransactions()
})

function removeArticleByIndex(index: number): void {
  const normalizedIndex: number = (cart.value.length - 1) - index;
  cart.value.splice(normalizedIndex, 1)
}

function reset() {
  cart.value = []
}

function sendOpenTransactions() {
  transactor.queue.length().then((queueCount: number) => {
    if (queueCount <= 0) {
      return
    }

    transactionState.value = new Sending(queueCount)
    transactor.sendAll((current, max, success) => {
      transactionState.value = new Sending(max - success)
    }).then((result) => {
      if (result.success >= result.max) {
        if (props.buttonSound) playSuccess()
        transactionState.value = new Success()
        setTimeout(() => transactionState.value = new None(), 2500)
      } else {
        transactionState.value = new Pending(result.max - result.success)
      }
    })
  })
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
