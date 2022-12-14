import React from 'react';
import './Container.scss';

function Container({ children }) {
    return (
        <div className="site-container">
            {children}
            {/* <div className="grid-child"></div> */}
        </div>
    );
}
export default Container;
