/**
 * Network Constellation Background Animation
 * Renders interconnected nodes with lines, slowly drifting across the canvas.
 */
(function () {
    'use strict';

    function initNetworkBg(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width, height, nodes = [], animId;
        const LINE_DIST = 160;
        const NODE_COUNT_FACTOR = 0.00012; // nodes per pixel area
        const SPEED = 0.15;

        function resize() {
            const parent = canvas.parentElement;
            width = canvas.width = parent.offsetWidth;
            height = canvas.height = parent.offsetHeight;
        }

        function createNodes() {
            const count = Math.max(40, Math.floor(width * height * NODE_COUNT_FACTOR));
            nodes = [];
            for (let i = 0; i < count; i++) {
                nodes.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * SPEED,
                    vy: (Math.random() - 0.5) * SPEED,
                    r: Math.random() * 3 + 1.5,
                    filled: Math.random() > 0.4,
                    opacity: Math.random() * 0.4 + 0.25
                });
            }
        }

        function update() {
            for (const n of nodes) {
                n.x += n.vx;
                n.y += n.vy;
                if (n.x < -20) n.x = width + 20;
                if (n.x > width + 20) n.x = -20;
                if (n.y < -20) n.y = height + 20;
                if (n.y > height + 20) n.y = -20;
            }
        }

        function draw() {
            ctx.clearRect(0, 0, width, height);

            // Draw lines
            for (let i = 0; i < nodes.length; i++) {
                for (let j = i + 1; j < nodes.length; j++) {
                    const dx = nodes[i].x - nodes[j].x;
                    const dy = nodes[i].y - nodes[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < LINE_DIST) {
                        const alpha = (1 - dist / LINE_DIST) * 0.18;
                        ctx.beginPath();
                        ctx.moveTo(nodes[i].x, nodes[i].y);
                        ctx.lineTo(nodes[j].x, nodes[j].y);
                        ctx.strokeStyle = `rgba(100, 130, 170, ${alpha})`;
                        ctx.lineWidth = 0.7;
                        ctx.stroke();
                    }
                }
            }

            // Draw nodes
            for (const n of nodes) {
                ctx.beginPath();
                ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
                if (n.filled) {
                    ctx.fillStyle = `rgba(80, 120, 170, ${n.opacity})`;
                    ctx.fill();
                } else {
                    ctx.strokeStyle = `rgba(100, 140, 190, ${n.opacity})`;
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }
        }

        function loop() {
            update();
            draw();
            animId = requestAnimationFrame(loop);
        }

        function init() {
            resize();
            createNodes();
            loop();
        }

        let resizeTimeout;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function () {
                resize();
                createNodes();
            }, 200);
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    }

    // Expose globally
    window.initNetworkBg = initNetworkBg;
})();
