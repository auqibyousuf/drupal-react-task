import React from 'react'
import './Button.scss'
const Button = ({ onClick, btnClass, btnText }) => {
  return (
    <button className={`btn ${btnClass}`} onClick={onClick}>
      {btnText}
    </button>
  )
}

export default Button
