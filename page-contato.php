<?php
/**
 * Template: Contato
 */
get_header();
?>

<main id="conteudo" class="page-content">
  <section class="section section--highlight">
    <div class="container">
      <header class="section__header section__header--center">
        <p class="eyebrow">Fale conosco</p>
        <h1><?php the_title(); ?></h1>
        <div class="section__lead">
          <p>Estamos à disposição para esclarecer dúvidas, enviar catálogos completos, apoiar na montagem de enxoval e oferecer propostas comerciais personalizadas.</p>
        </div>
      </header>

      <div class="contact">
        <div class="contact__text">
          <h2>Converse com um especialista</h2>
          <p>Envie sua lista de materiais ou solicite uma cotação personalizada. Retornamos em até duas horas úteis.</p>
          <div class="contact__hours">
            <strong>Atendimento de segunda a sexta</strong>
            <span>Horário: das 8h às 18h</span>
          </div>
        </div>
        <form class="contact__form" action="#" method="post">
          <div class="form-group">
            <label for="contact-name">Nome completo</label>
            <input id="contact-name" name="nome" type="text" autocomplete="name">
          </div>
          <div class="form-group">
            <label for="contact-company">Instituição</label>
            <input id="contact-company" name="instituicao" type="text" autocomplete="organization">
          </div>
          <div class="form-group">
            <label for="contact-email">E-mail corporativo</label>
            <input id="contact-email" name="email" type="email" autocomplete="email">
          </div>
          <div class="form-group">
            <label for="contact-message">Necessidade</label>
            <textarea id="contact-message" name="mensagem" rows="5"></textarea>
          </div>
          <button class="button" type="submit">Quero falar com a Fradel-Med</button>
        </form>
      </div>
    </div>
  </section>
</main>

<?php
get_footer();
