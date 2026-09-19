setup:
	bash setup.sh

worker:
	php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --queue=default,video

fresh:
	php artisan migrate:fresh --seed
