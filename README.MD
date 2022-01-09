# Symfony kafka example

## install
```cp .env.dist .env```

```docker-compose up -d```

```docker-compose exec app composer i```

## run app
```docker-compose exec app ./bin/console app:producer 500```

```docker-compose exec app ./bin/console app:consumer```