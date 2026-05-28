@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Testing Page') }}
@endsection

@section('content')
	<style>
	.radio-card
	{
		border: 2px solid rgba(0, 0, 0, 0.1);
		border-radius: 10px;
		width: 300px;
		height: 200px;
		padding: .25rem;
		transition: all 0.3s;
		position: relative;
	}

	.radio-card:hover 
	{
		border: 2px solid var(--ph-primary);
		cursor: pointer;
	}

	.radio-card-check
	{
		display: none;
		position: absolute;
		left: 0.75rem;
		bottom: 0.85rem;
		z-index: 1;
	}

	.radio-card-check i 
	{
		font-size: 1.6rem;
		color: var(--ph-primary);
	}

	.text-center 
	{
		text-align: center;
	}

	.radio-card-icon img
	{
		width: 80px;
	}

	.radio-card-label2
	{
		margin-top: 1rem;
		font-weight: 600;
		font-size: 1.2rem;
	}

	.radio-card-label-description
	{
		margin-top: 0.5rem;
		color: rgba(0, 0, 0, 0.7);
	}

	.radio-card.selected
	{
		border: 2px solid #016787;
	}

	.radio-card.selected 
	{
		border: 2px solid var(--ph-primary);
	}

	.radio-card.selected .radio-card-check
	{
		display: inline-flex;
	}

	.bg-image 
	{
		position: absolute;
		background-size: cover;
		background-position: center center;
		width: 100%;
		height: 100%;
		border-radius: 8px;
		/* z-index: -1; */
	}
	</style>

	<div class="arv7-content rounded p-4">
		<div class="mb-5">
			<div class="h5 mb-3">{{ t('Login View Settings') }}</div>

			<div class="row g-3">
				<div class="col-md-3">
					<div class="radio-card radio-card-default_login_view" onclick="selectRadioCard('default_login_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/default/default_login_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-card_login_view" onclick="selectRadioCard('card_login_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/card/card_login_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-split_left_login_view" onclick="selectRadioCard('split_left_login_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/split_left/split_left_login_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-split_right_login_view" onclick="selectRadioCard('split_right_login_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/split_right/split_right_login_view.png") }}');"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="mb-5">
			<div class="h5 mb-3">{{ t('Signup View Settings') }}</div>

			<div class="row g-3">
				<div class="col-md-3">
					<div class="radio-card radio-card-default_signup_view" onclick="selectRadioCard('default_signup_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/default/default_signup_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-card_signup_view" onclick="selectRadioCard('card_signup_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/card/card_signup_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-split_left_signup_view" onclick="selectRadioCard('split_left_signup_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/split_left/split_left_signup_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-split_right_signup_view" onclick="selectRadioCard('split_right_signup_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/split_right/split_right_signup_view.png") }}');"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div>
			<div class="h5 mb-3">{{ t('Forgot Password View Settings') }}</div>

			<div class="row g-3">
				<div class="col-md-3">
					<div class="radio-card radio-card-default_forgotpassword_view" onclick="selectRadioCard('default_forgotpassword_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/default/default_forgotpassword_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-card_forgotpassword_view" onclick="selectRadioCard('card_forgotpassword_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/card/card_forgotpassword_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-split_left_forgotpassword_view" onclick="selectRadioCard('split_left_forgotpassword_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/split_left/split_left_forgotpassword_view.png") }}');"></div>
						</div>
					</div>
				</div>

				<div class="col-md-3">
					<div class="radio-card radio-card-split_right_forgotpassword_view" onclick="selectRadioCard('split_right_forgotpassword_view')">
						<!-- Radio Card Check (tick) icon. By default, its hidden. Will be displayed when card gets clicked. -->
						<div class="radio-card-check">
							<span class="fa-layers fa-fw fa-2x">
								<i class="fas fa-circle text-white"></i>
								<i class="fas fa-check-circle" data-fa-transform="shrink-1" style="color: var(--ph-primary);"></i>
							</span>
						</div>

						<div class="position-relative h-100">
							<div class="bg-image" style="background-image: url('{{ asset("storage/page_themes/auth/split_right/split_right_forgotpassword_view.png") }}');"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
	const selectRadioCard = (cardNo) => 
	{
		/**
		 * Loop through all radio cards, and remove the class "selected" from those elements.
		 */
		const allRadioCards = document.querySelectorAll(".radio-card");
		allRadioCards.forEach((element, index) => {
			element.classList.remove(["selected"]);
		});
		/**
		 * Add the class "selected" to the card which user has clicked on.
		 */
		const selectedCard = document.querySelector(".radio-card-" + cardNo);
		selectedCard.classList.add(["selected"]);
	};

	const selectRadioCard2 = (cardNo) => 
	{
		/**
		 * Loop through all radio cards, and remove the class "selected" from those elements.
		 */
		const allRadioCards = document.querySelectorAll(".radio-card2");
		allRadioCards.forEach((element, index) => {
			element.classList.remove(["selected"]);
		});
		/**
		 * Add the class "selected" to the card which user has clicked on.
		 */
		const selectedCard = document.querySelector(".radio-card2-" + cardNo);
		selectedCard.classList.add(["selected"]);
	};
	</script>
@endsection

@pushonce('js')
	<script src="{{ url('assets/plugins/fontawesome/5.15.3/js/all.min.js') }}"></script>
@endpushonce