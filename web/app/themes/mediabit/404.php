<?php
get_header();


$header = new \Mediabit\Templates\Sections\Header();
echo $header->render(); 

?>
<section class="pt-5">
	<div class="container text-center pt-lg-6 pt-4">
		<div class="row justify-content-center">

			<div class="col-xl-7 col-lg-8 col-md-10">
				<div class="lc-block mt-4">
					<svg class="icon-set icon-primary mb-3" xmlns="http://www.w3.org/2000/svg" role="img">
						<title>book-square</title>
						<use xlink:href="/app/themes/mediabit/assets/icons/icons.svg#book-square"></use>
					</svg>
					<div editable="rich">
						<h2 class="rfs-25 fw-bolder">Pagina nu a fost găsită</h2>
					</div>
				</div>
			</div>

			<div class="col-lg-8 col-md-9 col-sm-10 col-xl-6">
				<div class="lc-block">
					<div editable="rich">
						<p>Pagina la care te uiți chiar acum este o carte deschisă la capitolul "În curs de scriere". Se lucrează la ea în fiecare zi pentru a-ți aduce conținut pe care abia aștepți să-l explorezi. Ai răbdare și pune-ne în bookmarks. E ca așteptarea înainte de a deschide un cadou de la o persoana dragă ție.
						</p>
                        <a href="/" class="btn btn-primary">Înapoi la pagina principală</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="lc-block">
					<img class="img-fluid wp-image-1149" src="http://mediabit.test/app/uploads/2023/09/building.jpg" width="1488" height="772" srcset="https://mediabit.test/app/uploads/2023/09/building.jpg 1488w, https://mediabit.test/app/uploads/2023/09/building-300x156.jpg 300w, https://mediabit.test/app/uploads/2023/09/building-1024x531.jpg 1024w, https://mediabit.test/app/uploads/2023/09/building-768x398.jpg 768w" sizes="(max-width: 1488px) 100vw, 1488px" alt="">
				</div>
			</div>
		</div>
	</div>
</section>
<?php

$footer = new \Mediabit\Templates\Sections\Footer();
echo $footer->render();

get_footer();
?>