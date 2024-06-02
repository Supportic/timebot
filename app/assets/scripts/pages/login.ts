import '@styles/pages/login.scss';

document.addEventListener('DOMContentLoaded', () => {
  const interBubble = document.getElementById(
    'interactive'
  ) as HTMLDivElement | null;

  if (!(interBubble instanceof HTMLDivElement)) return;

  let curX = 0,
    curY = 0,
    tgX = 0,
    tgY = 0;

  const move = (): void => {
    curX += (tgX - curX) / 20;
    curY += (tgY - curY) / 20;
    interBubble.style.transform = `translate(${Math.round(
      curX
    )}px, ${Math.round(curY)}px)`;
    requestAnimationFrame(move);
  };

  window.addEventListener('mousemove', (event) => {
    tgX = event.clientX;
    tgY = event.clientY;
  });

  move();
});
