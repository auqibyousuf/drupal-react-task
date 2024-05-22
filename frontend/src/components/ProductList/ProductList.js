import React, { useContext, useEffect, useState } from 'react'
import Product from '../Product/Product'
import './ProductList.scss'
import { CartContext } from '../../context/CartContext'
import toast, { Toaster } from 'react-hot-toast'
import Loader from '../Loader/Loader'

const ProductList = () => {
  const [products, setProducts] = useState([])
  const [isLoading, setIsLoading] = useState(false)
  const getData = async () => {
    try {
      const data = await fetch('https://drupal-project.ddev.site/products')
      const results = await data.json()
      setProducts(results)
    } catch (error) {
      toast.error('Failed to Fetch data', error)
    } finally {
      setIsLoading(false)
    }
  }
  useEffect(() => {
    setIsLoading(true)
    setTimeout(() => {
      getData()
    }, 2000)
  }, [])

  const { addToCart } = useContext(CartContext)
  return (
    <>
      <div>
        <Toaster position='top-center' />
      </div>
      <ul className='product-list-container'>
        {isLoading ? (
          <Loader />
        ) : (
          products.map((product) => {
            return (
              <li key={product.nid}>
                <Product
                  imgSrc={`${process.env.REACT_APP_DRUPAL_END_POINT}${product.field_product_image}`}
                  title={product.field_product_name}
                  price={product.field_product_price}
                  addBtnClick={() => {
                    addToCart(product)

                    toast.success('Item added to Cart.', {
                      style: {
                        border: '1px solid green',
                        padding: '16px',
                        color: 'green',
                      },
                      iconTheme: {
                        primary: 'green',
                        secondary: '#FFFAEE',
                      },
                    })
                  }}
                />
              </li>
            )
          })
        )}
      </ul>
    </>
  )
}

export default ProductList
