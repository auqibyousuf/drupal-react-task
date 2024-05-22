import Button from './components/Button/Button'
import ProductList from './components/ProductList/ProductList'
import './App.scss'
import { CartContext } from './context/CartContext'
import { useContext, useState } from 'react'

import Modal from './components/Modal/Modal'
function App() {
  const { cartItems } = useContext(CartContext)
  const [showModal, setShowModal] = useState(false)
  const toggle = () => {
    setShowModal(!showModal)
  }

  return (
    <div className='container'>
      <ProductList />
      {!showModal && (
        <Button
          btnClass={'cart'}
          btnText={`View Cart ${cartItems.length}`}
          onClick={toggle}
        />
      )}
      <Modal showModal={showModal} toggle={toggle} />
    </div>
  )
}

export default App
