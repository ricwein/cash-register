<template>
  <div v-if="useLandscapeMode" class="row w-100">
    <div class="receipt col-lg-4 col-md-5 col-sm-5">
      <number-display-side v-model="transactionState" :transactionState class="sticky-top" :price
                           :pendingQuantity="pendingQuantity"></number-display-side>
      <receipt-side :cart :numpadHeight="numpadHeightCss" :selectedIndex="selectedDisplayIndex"
                    @removeArticle="removeArticleByIndex" @selectArticle="selectedDisplayIndex = $event"></receipt-side>
      <quantity-numpad
          v-if="articleQuantitySelection"
          ref="numpad"
          v-model="pendingQuantity"
          :buttonSound
      ></quantity-numpad>
      <div v-if="quickCheckout" class="row action-button-row sticky-bottom">
        <backspace-button class="col-4" :useLandscapeMode :buttonSound @backspaceClicked="backspaceClicked"
                          @createNewReceipt="reset"></backspace-button>
        <checkout-button v-if="cart.length <= 0 || price > 0.0" class="col-4" :buttonSound :cart
                         @registerConfirmed="transition" :type="CheckoutTransition.Card"></checkout-button>
        <checkout-button v-if="cart.length <= 0 || price > 0.0" class="col-4" :buttonSound :cart
                         @registerConfirmed="transition" :type="CheckoutTransition.Cash"></checkout-button>
        <checkout-button v-else-if="price < 0.0" class="col-8" :buttonSound :cart @registerConfirmed="transition"
                         :type="CheckoutTransition.Payout"></checkout-button>
        <checkout-button v-else class="col-8" :buttonSound :cart @registerConfirmed="transition"
                         :type="CheckoutTransition.Continue"></checkout-button>
      </div>
      <div v-else class="row action-button-row sticky-bottom">
        <backspace-button class="col" :useLandscapeMode :buttonSound @backspaceClicked="backspaceClicked"
                          @createNewReceipt="reset"></backspace-button>
        <checkout-button class="col" :buttonSound :cart @registerConfirmed="transition"
                         :type="CheckoutTransition.Start"></checkout-button>
      </div>
    </div>
    <div class="products col">
      <product-selection-tabbed v-if="showCategoryTabs" :useLandscapeMode :buttonSound :categories
                                :displayHeightPortrait :historyHeightPortrait numpadHeightPortrait="0px"
                                :gridWidthElements @product-clicked="addToCart"></product-selection-tabbed>
      <product-selection v-else :buttonSound :categories :displayHeightPortrait :historyHeightPortrait
                         numpadHeightPortrait="0px" :gridWidthElements @product-clicked="addToCart"></product-selection>
    </div>
  </div>
  <div v-else>
    <div class="sticky-top">
      <number-display v-model="transactionState" :quickCheckout :buttonSound :transactionState :price :cart
                      :displayHeightPortrait :pendingQuantity="pendingQuantity"
                      @registerConfirmed="transition"></number-display>
      <receipt :useLandscapeMode :buttonSound :cart :historyHeightPortrait :selectedIndex="selectedDisplayIndex"
               @removeArticle="removeArticleByIndex" @selectArticle="selectedDisplayIndex = $event"
               @backspaceClicked="backspaceClicked" @createNewReceipt="reset"></receipt>
      <quantity-numpad
          v-if="articleQuantitySelection"
          ref="numpad"
          v-model="pendingQuantity"
          :buttonSound
      ></quantity-numpad>
    </div>
    <product-selection-tabbed v-if="showCategoryTabs" :useLandscapeMode :buttonSound :categories :displayHeightPortrait
                              :historyHeightPortrait :numpadHeightPortrait="numpadHeightForProducts" :gridWidthElements
                              @product-clicked="addToCart"></product-selection-tabbed>
    <product-selection v-else :buttonSound :categories :displayHeightPortrait :historyHeightPortrait
                       :numpadHeightPortrait="numpadHeightForProducts" :gridWidthElements
                       @product-clicked="addToCart"></product-selection>
  </div>

  <checkout
      v-model="checkoutState"
      :buttonSound :lazyCalculator :price :cart :transactor
      @checkoutCancelled="checkoutState.dispatch(CheckoutTransition.Cancel)"
      @createNewReceipt="reset"
  ></checkout>
</template>

<script setup lang="ts">
import {computed, onMounted, type PropType, ref, shallowRef, useTemplateRef} from "vue";
import {useElementSize} from "@vueuse/core";
import {useSound} from "@vueuse/sound";
import {CheckoutStateMachine, CheckoutTransition} from "../../components/checkout-state-machine.ts";
import Category from "../../model/category";
import Product from "../../model/product";
import type CartItem from "../../model/cart-item.ts";
import NumberDisplay from "../components/receipt/display/number-display.vue";
import NumberDisplaySide from "../components/receipt/display/number-display-side.vue";
import Receipt from "../components/receipt/receipt.vue";
import ReceiptSide from "../components/receipt/receipt-side.vue";
import CheckoutButton from "../components/receipt/buttons/checkout-button.vue";
import BackspaceButton from "../components/receipt/buttons/backspace-button.vue";
import ProductSelection from "../components/selection/product-selection.vue";
import ProductSelectionTabbed from "../components/selection/product-selection-tabbed.vue";
import Checkout from "../components/checkout/checkout.vue";
import QuantityNumpad from "../components/receipt/buttons/quantity-numpad.vue";
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
  articleQuantitySelection: {type: Boolean, default: false},
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

const numpadRef = useTemplateRef<any>('numpad')
const {height: numpadPx} = useElementSize(numpadRef, {width: 0, height: 0}, {box: 'border-box'})
const numpadHeightCss = computed(() => `${numpadPx.value}px`)
// In landscape mode the numpad lives in the receipt column — product grid unaffected
const numpadHeightForProducts = computed(() =>
    props.useLandscapeMode ? '0px' : numpadHeightCss.value
)

const cart = ref<CartItem[]>([])
const pendingQuantity = ref<number | null>(null)
const selectedDisplayIndex = ref<number | null>(null)

const price = computed<number>(() => cart.value
    .reduce((sum: number, item: CartItem) => sum + item.product.price * item.quantity, 0.0)
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

function addToCart(product: Product): void {
  const qty = pendingQuantity.value ?? 1
  const existing = cart.value.find(item => item.product.id === product.id)
  if (existing) {
    existing.quantity += qty
    selectedDisplayIndex.value = (cart.value.length - 1) - cart.value.indexOf(existing)
  } else {
    cart.value.push({product, quantity: qty})
    selectedDisplayIndex.value = 0
  }
  pendingQuantity.value = null
}

function backspaceClicked(): void {
  if (pendingQuantity.value !== null) {
    const next = Math.floor(pendingQuantity.value / 10)
    pendingQuantity.value = next === 0 ? null : next
  } else if (selectedDisplayIndex.value !== null) {
    removeArticleByIndex(selectedDisplayIndex.value)
  }
}

function removeArticleByIndex(displayIndex: number): void {
  const normalizedIndex = (cart.value.length - 1) - displayIndex
  cart.value.splice(normalizedIndex, 1)
  if (cart.value.length === 0) {
    selectedDisplayIndex.value = null
  } else {
    selectedDisplayIndex.value = Math.min(displayIndex, cart.value.length - 1)
  }
}

function reset() {
  cart.value = []
  selectedDisplayIndex.value = null
}

function transition(to: CheckoutTransition) {
  checkoutState.value.dispatch(to)
}

function sendOpenTransactions() {
  transactor.queue.length().then((queueCount: number) => {
    if (queueCount <= 0) {
      transactionState.value = new None()
      return
    }

    transactionState.value = new Sending(queueCount)
    transactor
        .sendAll((current, max, success) => transactionState.value = new Sending(max - success))
        .then((result) => {
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
