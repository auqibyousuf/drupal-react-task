import React, { useContext } from 'react'
import { CartContext } from '../../context/CartContext'
import Heading from '../Heading/Heading'
import { FiMinus, FiPlus, FiShoppingCart } from 'react-icons/fi'
import { CgClose } from 'react-icons/cg'
import { MdOutlineDeleteOutline } from 'react-icons/md'
import './Modal.scss'

const Modal = ({ showModal, toggle }) => {
  const {
    cartItems,
    addToCart,
    removeFromCart,
    removeItemFromCart,
    getCartTotal,
    getItemTotal,
  } = useContext(CartContext)

  return (
    showModal && (
      <div className='modal-overlay'>
        <div className='modal-container'>
          <div className='modal-header'>
            <p>
              <FiShoppingCart />
              Order Summary
            </p>
            <CgClose onClick={toggle} className='close-icon' />
          </div>
          {cartItems.length > 0 ? (
            <table className='modal-body'>
              <thead>
                <tr>
                  <th></th>
                  <th className='table-heading-name'>Name</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                {cartItems.map((item) => {
                  return (
                    <tr className='cart-item' key={item.nid}>
                      <td className='cart-item__image'>
                        <img
                          src={`${process.env.REACT_APP_DRUPAL_END_POINT}${item.field_product_image}`}
                          alt={'image'}
                        />
                      </td>
                      <td className='cart-item__name'>
                        <p>{item.field_product_name}</p>
                      </td>

                      <td className='cart-item__price'>
                        <p>${item.field_product_price}</p>
                      </td>
                      <td className='cart-item__quantity'>
                        <div className='quantity'>
                          <FiMinus
                            onClick={() => {
                              removeFromCart(item)
                            }}
                          />
                          <span>{item.quantity}</span>
                          <FiPlus
                            onClick={() => {
                              addToCart(item)
                            }}
                          />
                        </div>
                      </td>
                      <td className='cart-item__total'>
                        <span>${getItemTotal(item)}</span>
                      </td>
                      <td className='cart-item__delete-icon'>
                        <MdOutlineDeleteOutline
                          onClick={() => {
                            removeItemFromCart(item)
                          }}
                        />
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          ) : (
            <p>Your Cart is Empty</p>
          )}

          <div className='modal-footer'>
            <p>
              Total <span>${getCartTotal()}</span>
            </p>
          </div>
        </div>
      </div>
    )
  )
}

export default Modal
