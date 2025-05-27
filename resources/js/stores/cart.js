import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: JSON.parse(localStorage.getItem('cartItems')) || []
  }),
  actions: {
    addToCart(product) {
      this.items.push(product)
      this.saveCart()
    },
    removeFromCart(index) {
      this.items.splice(index, 1)
      this.saveCart()
    },
    saveCart() {
      localStorage.setItem('cartItems', JSON.stringify(this.items))
    },
    clearCart() {
      this.items = []
      localStorage.removeItem('cartItems')
    }
  }
})