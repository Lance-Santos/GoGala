import './bootstrap';

import InfiniteViewer from "infinite-viewer";

document.addEventListener("DOMContentLoaded", () => {
    const infiniteViewer = new InfiniteViewer(
        document.querySelector(".viewer"),
        document.querySelector(".viewport"), {
            useAutoZoom: true,
            zoom: 0.9,
        }
    );

    // Scroll Event
    infiniteViewer.on("scroll", () => {
        console.log("scrolling-", infiniteViewer.getScrollLeft(), infiniteViewer.getScrollTop());
    });

    // Center Me Button
    document.querySelector('[data="centerMe"]').addEventListener("click", () => {
        infiniteViewer.scrollCenter({ duration: 300 });
    });

    // Zoom In and Center Button
    document.querySelector('[data="zoomIn"]').addEventListener("click", () => {
        infiniteViewer.setZoom(8, { duration: 300 });
        infiniteViewer.scrollCenter({ duration: 300 });
    });

    // Get Zoom Button
    document.querySelector('[data="getZoom"]').addEventListener("click", () => {
        console.log("Current Zoom:", infiniteViewer.getZoom());
    });

    // Item Click Event
    document.querySelectorAll('[data="item"]').forEach((item) => {
        item.addEventListener("click", () => {
            console.log("Item clicked");
            infiniteViewer.setZoom(8, { duration: 300 });
            infiniteViewer.scrollCenter({ duration: 300 });
        });
    });
});
