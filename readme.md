## Escooters

### Last build
```
Build date: 2022-03-19 11:18:28

170 cities fetched for Bolt.
149 cities fetched for Lime.
23 cities fetched for Quick.
188 cities fetched for Tier.
78 cities fetched for Voi.
36 cities fetched for Link.
57 cities fetched for Spin.
24 cities fetched for Neuron.
40 cities fetched for Helbiz.
18 cities fetched for Whoosh.
111 cities fetched for Bird.
31 cities fetched for Dott.

594 cities fetched.
Cached cities loaded.
```

### Available providers

| No. | Provider | Data source |
|---|---|---|
| 1 | Lime | webscrapped |
| 2 | Bolt | web API |
| 3 | Tier | web API |
| 4 | Bird | webscrapped with partially estimated countries |
| 5 | Voi | webscrapped |
| 6 | Spin | webscrapped |
| 7 | Link | webscrapped |
| 8 | Dott | webscrapped with partially estimated countries |
| 9 | Quick | webscrapped |
| 10 | Neuron | partially webscrapped |
| 11 | Whoosh | hardcoded |
| 12 | Helbiz | hardcoded |

### Screenshot

![./screenshot.png](./screenshot.png)

### Development

```
copy .env.example .env
docker-compose run --rm -u "$(id -u):$(id -g)" php composer install
docker-compose run --rm -u "$(id -u):$(id -g)" php php index.php
```
