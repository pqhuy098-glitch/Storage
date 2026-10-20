using Avalonia;
using System;

namespace OptiRoute;

class Program
{
    // Initialization code. Don't use any Avalonia, third-party APIs or any
    // SynchronizationContext-reliant code before AppMain is called: things aren't initialized
    // yet and stuff might break.
    [STAThread]
    public static void Main(string[] args) => BuildAvaloniaApp()
        .StartWithClassicDesktopLifetime(args);

    // Avalonia configuration, don't remove; also used by visual designer.
    public static AppBuilder BuildAvaloniaApp()
        => AppBuilder.Configure<App>()
            .UsePlatformDetect()
#if DEBUG
            .WithDeveloperTools()
#endif
            .WithInterFont()
            // 🌟 Cấp phát 512MB VRAM Cache cho Skia Engine giúp RTX 3050 Ti render mượt mà hơn
            .With(new SkiaOptions 
            { 
                MaxGpuResourceSizeBytes = 512 * 1024 * 1024 
            })
            .LogToTrace();
}