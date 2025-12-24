<template>
  <div class="page">
    <!-- HEADER -->
    <header class="header">
      PLANORA
    </header>

    <!-- MAIN -->
    <main class="main">
      <section class="hero-content">
        <h1>
          Manage your <br />
          projects <span>smarter.</span><br />
          Not harder
        </h1>

        <p>
          Plan better. Work together. Deliver faster.<br />
          With Planora!
        </p>

        <p>Together, we make your projects succeed.</p>

        <router-link to="/register">
          <button class="btn">Sign up for free</button>
        </router-link>

        <div class="small-text">
          Bạn đã có tài khoản?
          <router-link class="login" to="/login">Log in</router-link>
        </div>
      </section>

      <section class="right">
        <img
          src="https://online.champlain.edu/sites/online/files/styles/width_1600/public/2024-10/blog-header-project-mgmt-tips.jpg?itok=GVBIh1Ec"
          alt="Promo Image"
        />
      </section>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
      <div>Have questions? Call us at 0123456789.</div>

      <div class="footer-right">
        <a href="https://www.facebook.com/" target="_blank" rel="noopener">
          Facebook
        </a>
        <a href="https://www.instagram.com/" target="_blank" rel="noopener">
          Instagram
        </a>
        <a href="https://www.tiktok.com/" target="_blank" rel="noopener">
          TikTok
        </a>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'Landing'
}
</script>

<style scoped>
/* RESET */
.page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: Arial, sans-serif;
}

/* HEADER */
.header {
  background: #e5e5e5;
  padding: 15px 30px;
  font-size: 20px;
  font-weight: bold;
}

/* MAIN */
.main {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 70px;
}

.hero-content {
  text-align: center;
  margin-left: 160px;
}

.hero-content h1 {
  font-size: 42px;
  font-weight: 700;
}

.hero-content p {
  margin: 10px 0;
}

/* BUTTON */
.btn {
  margin-top: 20px;
  padding: 12px 25px;
  background: #ddd;
  border: 1px solid #000;
  cursor: pointer;
  border-radius: 5px;
  font-size: 16px;
}

/* SMALL TEXT */
.small-text {
  margin-top: 10px;
  font-size: 14px;
}

.small-text a {
  text-decoration: underline;
  color: black;
  margin-left: 5px;
}

/* RIGHT IMAGE */
.right {
  width: 40%;
  height: 380px;
  background: #dcdcdc;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
  margin-right: 100px;
}

.right img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* FOOTER */
.footer {
  background: #e5e5e5;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  font-size: 14px;
}

.footer-right a {
  margin-left: 20px;
  text-decoration: none;
  color: black;
  font-weight: bold;
}
</style>