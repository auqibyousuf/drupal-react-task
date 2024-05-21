import React, { createContext, useEffect, useState } from 'react'
import toast from 'react-hot-toast'

export const CartContext = createContext()
const CartContextProvider = ({ children }) => {
  const [cartItems, setCartItems] = useState(
    localStorage.getItem('cartItems')
      ? JSON.parse(localStorage.getItem('cartItems'))
      : []
  )

  const findCartItem = (item) => {
    return cartItems.find((cartItem) => cartItem.nid === item.nid)
  }
  const addToCart = (item) => {
    const isItemInCart = findCartItem(item)
    if (isItemInCart) {
      const maxQuantity = item.field_product_stock
      if (isItemInCart.quantity < maxQuantity) {
        setCartItems(
          cartItems.map((cartItem) =>
            cartItem.nid === item.nid
              ? { ...cartItem, quantity: cartItem.quantity + 1 }
              : cartItem
          )
        )
      } else {
        toast.error('Cannot add more items', {
          style: {
            border: '1px solid #FF3333',
            padding: '16px',
            color: '#FF3333',
          },
          iconTheme: {
            primary: '#FF3333',
            secondary: '#FFFAEE',
          },
        })
      }
    } else {
      setCartItems([...cartItems, { ...item, quantity: 1 }])
    }
  }

  const removeFromCart = (item) => {
    const isItemInCart = findCartItem(item)
    if (isItemInCart && isItemInCart.quantity === 1) {
      setCartItems(cartItems.filter((cartItem) => cartItem.nid !== item.nid))
    } else if (isItemInCart) {
      setCartItems(
        cartItems.map((cartItem) =>
          cartItem.nid === item.nid
            ? { ...cartItem, quantity: cartItem.quantity - 1 }
            : cartItem
        )
      )
    }
  }

  const removeItemFromCart = (item) => {
    setCartItems((currentCartItem) =>
      currentCartItem.filter((cartItem) => cartItem.nid !== item.nid)
    )
  }

  const getCartTotal = () => {
    return cartItems.reduce(
      (total, item) => total + item.field_product_price * item.quantity,
      0
    )
  }

  const getItemTotal = (item) => {
    const isItemInCart = findCartItem(item)
    return isItemInCart
      ? isItemInCart.field_product_price * isItemInCart.quantity
      : 0
  }

  useEffect(() => {
    localStorage.setItem('cartItems', JSON.stringify(cartItems))
  }, [cartItems])

  return (
    <CartContext.Provider
      value={{
        cartItems,
        addToCart,
        removeFromCart,
        removeItemFromCart,
        getCartTotal,
        getItemTotal,
      }}
    >
      {children}
    </CartContext.Provider>
  )
}

export default CartContextProvider
