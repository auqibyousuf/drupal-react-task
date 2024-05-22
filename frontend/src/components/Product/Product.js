import React from 'react'
import Heading from '../Heading/Heading'
import Button from '../Button/Button'
import './Product.scss'

const Product = ({ imgSrc, title, price, addBtnClick }) => {
  return (
    <div className='product-container'>
      <img src={imgSrc} alt='product-image' className='product-image' />
      <div className='product-body'>
        <Heading headingText={title} />
        <div className='product-footer'>
          <span>${price}</span>
          <Button
            btnText={'Add to cart'}
            onClick={addBtnClick}
            btnClass={'product-button'}
          />
        </div>
      </div>
    </div>
  )
}

export default Product
