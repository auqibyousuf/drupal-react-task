import React from 'react'
import { BiLoaderAlt } from 'react-icons/bi'
import './Loader.scss'

const Loader = () => {
  return (
    <div className='loader-container'>
      <BiLoaderAlt size={40} className='loader-icon' />
      <p>Loading ...</p>
    </div>
  )
}

export default Loader
