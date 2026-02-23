FROM golang:1.24-alpine AS builder
WORKDIR /app

COPY services/go/go.mod ./
COPY services/go/go.sum ./
RUN go mod download

COPY services/go/ ./

RUN go build -o pdf-app ./main.go

FROM alpine:latest
WORKDIR /app

COPY --from=builder /app/pdf-app .

COPY --from=builder /app/fonts ./fonts

EXPOSE 50051

CMD ["./pdf-app"]