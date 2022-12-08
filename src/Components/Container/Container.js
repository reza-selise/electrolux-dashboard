import React from 'react';
import './Container.scss';

function Container({ children }) {
    return <div className="site-container">{children}</div>;
}
export default Container;
