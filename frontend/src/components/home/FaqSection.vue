<script setup>
import { onMounted } from "vue";

onMounted(() => {
	document.querySelectorAll(".faq-header").forEach((header) => {
		header.addEventListener("click", () => {
			const currentFaq = header.closest(".faq-item");

			document.querySelectorAll(".faq-item").forEach((item) => {
				item.classList.remove("faq-active");
			});

			currentFaq.classList.add("faq-active");
		});
	});
});
</script>

<template>
	<section id="faq" class="faq">
		<div class="container">
			<div class="text-center mb-5">
				<div class="section-title">
					<h2>Frequently Asked Questions</h2>
					<p>
						Necessitatibus eius consequatur ex aliquid fuga eum
						quidem sint consectetur velit
					</p>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-9">
					<div class="faq-wrapper">
						<div
							class="faq-item faq-active"
							data-aos="fade-up"
							data-aos-delay="150"
						>
							<div class="faq-header">
								<span class="faq-number">01</span>
								<h4>
									Donec sollicitudin molestie malesuada proin
									eget tortor?
								</h4>
								<div class="faq-toggle">
									<i class="bi bi-plus"></i>
									<i class="bi bi-dash"></i>
								</div>
							</div>
							<div class="faq-content">
								<div class="content-inner">
									<p>
										Curabitur arcu erat, accumsan id
										imperdiet et, porttitor at sem. Donec
										rutrum congue leo eget malesuada.
										Vestibulum ante ipsum primis in faucibus
										orci luctus et ultrices posuere cubilia
										curae.
									</p>
								</div>
							</div>
						</div>

						<div
							class="faq-item"
							data-aos="fade-up"
							data-aos-delay="200"
						>
							<div class="faq-header">
								<span class="faq-number">02</span>
								<h4>
									Sed porttitor lectus nibh vivamus magna
									justo?
								</h4>
								<div class="faq-toggle">
									<i class="bi bi-plus"></i>
									<i class="bi bi-dash"></i>
								</div>
							</div>
							<div class="faq-content">
								<div class="content-inner">
									<p>
										Nulla porttitor accumsan tincidunt.
										Mauris blandit aliquet elit, eget
										tincidunt nibh pulvinar a. Cras
										ultricies ligula sed magna dictum porta.
										Vivamus suscipit tortor eget felis
										porttitor volutpat.
									</p>
								</div>
							</div>
						</div>

						<div
							class="faq-item"
							data-aos="fade-up"
							data-aos-delay="250"
						>
							<div class="faq-header">
								<span class="faq-number">03</span>
								<h4>
									Pellentesque habitant morbi tristique
									senectus?
								</h4>
								<div class="faq-toggle">
									<i class="bi bi-plus"></i>
									<i class="bi bi-dash"></i>
								</div>
							</div>
							<div class="faq-content">
								<div class="content-inner">
									<p>
										Quisque velit nisi, pretium ut lacinia
										in, elementum id enim. Vestibulum ac
										diam sit amet quam vehicula elementum
										sed sit amet dui. Donec sollicitudin
										molestie malesuada.
									</p>
								</div>
							</div>
						</div>

						<div
							class="faq-item"
							data-aos="fade-up"
							data-aos-delay="300"
						>
							<div class="faq-header">
								<span class="faq-number">04</span>
								<h4>
									Lorem ipsum dolor sit amet consectetur
									adipiscing?
								</h4>
								<div class="faq-toggle">
									<i class="bi bi-plus"></i>
									<i class="bi bi-dash"></i>
								</div>
							</div>
							<div class="faq-content">
								<div class="content-inner">
									<p>
										Sed ut perspiciatis unde omnis iste
										natus error sit voluptatem accusantium
										doloremque laudantium. Totam rem
										aperiam, eaque ipsa quae ab illo
										inventore veritatis et quasi architecto.
									</p>
								</div>
							</div>
						</div>

						<div
							class="faq-item"
							data-aos="fade-up"
							data-aos-delay="350"
						>
							<div class="faq-header">
								<span class="faq-number">05</span>
								<h4>
									Curabitur aliquet quam id dui posuere
									blandit?
								</h4>
								<div class="faq-toggle">
									<i class="bi bi-plus"></i>
									<i class="bi bi-dash"></i>
								</div>
							</div>
							<div class="faq-content">
								<div class="content-inner">
									<p>
										At vero eos et accusamus et iusto odio
										dignissimos ducimus qui blanditiis
										praesentium voluptatum deleniti atque
										corrupti quos dolores et quas molestias
										excepturi sint occaecati.
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<style scoped>
.faq {
	padding: 7rem 0;
}

.faq .faq-item {
	margin-bottom: 0;
	border-bottom: 1px solid
		color-mix(in srgb, var(--default-color), transparent 90%);
	transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.faq .faq-item:last-child {
	border-bottom: none;
}

.faq .faq-item.faq-active .faq-header .faq-number {
	color: var(--accent-color);
	font-weight: 500;
}

.faq .faq-item.faq-active .faq-header h4 {
	color: var(--accent-color);
}

.faq .faq-item.faq-active .faq-header .faq-toggle {
	color: var(--accent-color);
	transform: rotate(0deg);
}

.faq .faq-item.faq-active .faq-header .faq-toggle i.bi-plus {
	opacity: 0;
	transform: rotate(90deg);
}

.faq .faq-item.faq-active .faq-header .faq-toggle i.bi-dash {
	opacity: 1;
	transform: rotate(0deg);
}

.faq .faq-item.faq-active .faq-content {
	grid-template-rows: 1fr;
	visibility: visible;
	opacity: 1;
}

.faq .faq-item:not(.faq-active) .faq-header .faq-toggle i.bi-plus {
	opacity: 1;
	transform: rotate(0deg);
}

.faq .faq-item:not(.faq-active) .faq-header .faq-toggle i.bi-dash {
	opacity: 0;
	transform: rotate(-90deg);
}

.faq .faq-item .faq-header {
	display: flex;
	align-items: center;
	padding: 40px 0;
	cursor: pointer;
	gap: 0;
	transition: all 0.3s ease;
}

.faq .faq-item .faq-header:hover .faq-number {
	transform: scale(1.1);
}

.faq .faq-item .faq-header:hover .faq-toggle {
	transform: scale(1.1);
}

.faq .faq-item .faq-header .faq-number {
	flex-shrink: 0;
	width: 80px;
	font-family: var(--heading-font);
	font-size: 1.1rem;
	font-weight: 300;
	color: color-mix(in srgb, var(--default-color), transparent 50%);
	transition: all 0.3s ease;
	letter-spacing: -0.02em;
}

.faq .faq-item .faq-header h4 {
	flex: 1;
	margin: 0;
	font-family: var(--heading-font);
	font-size: 1.25rem;
	font-weight: 300;
	color: var(--heading-color);
	transition: all 0.3s ease;
	line-height: 1.5;
	letter-spacing: -0.01em;
	margin-right: 20px;
}

.faq .faq-item .faq-header .faq-toggle {
	flex-shrink: 0;
	width: 24px;
	height: 24px;
	display: flex;
	align-items: center;
	justify-content: center;
	color: color-mix(in srgb, var(--default-color), transparent 40%);
	font-size: 18px;
	transition: all 0.3s ease;
	position: relative;
}

.faq .faq-item .faq-header .faq-toggle i {
	position: absolute;
	transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.faq .faq-item .faq-content {
	display: grid;
	grid-template-rows: 0fr;
	transition: 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
	visibility: hidden;
	opacity: 0;
}

.faq .faq-item .faq-content .content-inner {
	padding: 0 80px 40px 80px;
	overflow: hidden;
}

.faq .faq-item .faq-content .content-inner p {
	margin: 0;
	color: color-mix(in srgb, var(--default-color), transparent 25%);
	line-height: 1.8;
	font-size: 1rem;
	font-weight: 300;
}

@media (max-width: 768px) {
	.faq .faq-item .faq-header {
		padding: 30px 0;
	}

	.faq .faq-item .faq-header .faq-number {
		width: 60px;
		font-size: 1rem;
	}

	.faq .faq-item .faq-header h4 {
		font-size: 1.1rem;
		margin-right: 15px;
	}

	.faq .faq-item .faq-header .faq-toggle {
		width: 20px;
		height: 20px;
		font-size: 16px;
	}

	.faq .faq-item .faq-content .content-inner {
		padding: 0 60px 30px 60px;
	}

	.faq .faq-item .faq-content .content-inner p {
		font-size: 0.95rem;
	}
}

@media (max-width: 576px) {
	.faq .faq-item .faq-header {
		padding: 25px 0;
	}

	.faq .faq-item .faq-header .faq-number {
		width: 50px;
		font-size: 0.9rem;
	}

	.faq .faq-item .faq-header h4 {
		font-size: 1rem;
		margin-right: 10px;
	}

	.faq .faq-item .faq-content .content-inner {
		padding: 0 50px 25px 50px;
	}

	.faq .faq-item .faq-content .content-inner p {
		font-size: 0.9rem;
	}
}
</style>
