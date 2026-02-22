package main

import (
	"fmt"
	"log"
	"net"
)

func main() {
	fmt.Println("🚀 Go мікросервіс запускається...")

	port := ":50051"
	_, err := net.Listen("tcp", port)
	if err != nil {
		log.Fatalf("❌ Не вдалося запустити слухача на порту %s: %v", port, err)
	}

	fmt.Printf("✅ Сервер успішно слухає порт %s. Очікування запитів...\n", port)

	select {}
}
