package main

import (
	"context"
	"fmt"
	"github.com/johnfercher/maroto/pkg/color"
	"github.com/johnfercher/maroto/pkg/consts"
	"github.com/johnfercher/maroto/pkg/pdf"
	"github.com/johnfercher/maroto/pkg/props"
	pb "go-service/pb"
	"google.golang.org/grpc"
	"log"
	"net"
)

type PdfServer struct {
	pb.UnimplementedReportServiceServer
}

func (s *PdfServer) GenerateTablePdf(ctx context.Context, req *pb.UniversalTableRequest) (*pb.PdfResponse, error) {
	fmt.Printf("[INFO] Received request to generate report: %s\n", req.Title)

	m := pdf.NewMaroto(consts.Landscape, consts.A4)

	m.AddUTF8Font("Roboto", consts.Normal, "fonts/Roboto-Regular.ttf")
	m.AddUTF8Font("Roboto", consts.Bold, "fonts/Roboto-Bold.ttf")
	m.SetDefaultFontFamily("Roboto")

	m.RegisterHeader(func() {
		m.Row(25, func() {
			m.Col(12, func() {
				m.Text(req.Title, props.Text{
					Top:   10,
					Style: consts.Bold,
					Align: consts.Center,
					Size:  18,
				})
			})
		})
	})

	var tableData [][]string
	for _, row := range req.Rows {
		tableData = append(tableData, row.Cells)
	}

	cols := len(req.Headers)
	var gridSizes []uint

	if cols > 0 {
		gridSizes = make([]uint, cols)
		baseSize := uint(12 / cols)
		remainder := uint(12 % cols)

		for i := 0; i < cols; i++ {
			gridSizes[i] = baseSize
		}
		gridSizes[0] += remainder
	}

	m.TableList(req.Headers, tableData, props.TableList{
		HeaderProp: props.TableListContent{
			Style:     consts.Bold,
			Size:      11,
			GridSizes: gridSizes,
		},
		ContentProp: props.TableListContent{
			Size:      10,
			GridSizes: gridSizes,
		},
		Align: consts.Left,

		HeaderContentSpace: 1,

		Line: true,

		AlternatedBackground: &color.Color{
			Red:   245,
			Green: 245,
			Blue:  245,
		},
	})

	buf, err := m.Output()
	if err != nil {
		fmt.Printf("[ERROR] Failed to generate PDF: %v\n", err)
		return nil, err
	}

	response := &pb.PdfResponse{
		FileData: buf.Bytes(),
	}

	fmt.Println("[SUCCESS] PDF successfully generated")
	return response, nil
}

func main() {
	port := ":50051"
	lis, err := net.Listen("tcp", port)
	if err != nil {
		log.Fatalf("[FATAL] Failed to listen on port %s: %v", port, err)
	}

	grpcServer := grpc.NewServer()
	pb.RegisterReportServiceServer(grpcServer, &PdfServer{})

	fmt.Printf("[INFO] gRPC Server is running and listening on port %s...\n", port)

	if err := grpcServer.Serve(lis); err != nil {
		log.Fatalf("[FATAL] Failed to serve gRPC server: %v", err)
	}
}
