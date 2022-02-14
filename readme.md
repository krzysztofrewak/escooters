## Escooters

### Available providers

| No. | Provider | Data source |
|---|---|---|
| 1 | Lime | webscrapped |
| 2 | Bolt | web API |
| 3 | Tier | webscrapped |
| 4 | Bird | webscrapped with partially estimated countries |
| 5 | Voi | webscrapped |
| 6 | Spin | webscrapped |
| 7 | Link | webscrapped |
| 8 | Dott | webscrapped with partially estimated countries |
| 9 | Quick | webscrapped |
| 10 | Neuron | partially webscrapped |
| 11 | Whoosh | hardcoded |

### Screenshot

![./screenshot.png](./screenshot.png)

### Development

```
copy .env.example .env
docker-compose run --rm -u "$(id -u):$(id -g)" php composer install
docker-compose run --rm -u "$(id -u):$(id -g)" php php index.php
```
